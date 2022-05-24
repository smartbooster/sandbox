<?php

namespace App\Mailer;

use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @author Mathieu Ducrot <mathieu.ducrot@smartbooster.io>
 */
class EmailProvider
{
    private string $locale;
    private ?array $emails = null;

    public function __construct(RequestStack $requestStack)
    {
        $this->locale = $requestStack->getCurrentRequest()->getLocale();
    }

    private function getEmailsConfiguration(): array
    {
        return [
            'admin.security.forgot_password',
            'admin.security.user_created',
        ];
    }

    public function getEmails(): array
    {
        if ($this->emails !== null) {
            return $this->emails;
        }

        $toReturn = [];

        foreach ($this->getEmailsConfiguration() as $code) {
            $toReturn[$code] = new TemplatedEmail($code, $this->locale);
        }

        $this->emails = $toReturn;

        return $toReturn;
    }

    /**
     * What we call domain for email is the first dotted string on an email code
     * For example, the domain for the email code 'admin.security.forgot_password' will be 'admin'
     */
    public function getEmailsGroupByDomain(): array
    {
        $domains = [];
        foreach ($this->getEmailsConfiguration() as $code) {
            $domains[] = substr($code, 0, (int) strpos($code, '.'));
        }
        $domains = array_unique($domains);

        $toReturn = [];
        foreach ($domains as $domain) {
            $toReturn[$domain] = $this->filterEmailsByDomain($domain);
        }

        return $toReturn;
    }

    private function filterEmailsByDomain(string $domain): array
    {
        $toReturn = $this->getEmails();

        //@phpstan-ignore-next-line
        return array_filter($toReturn, function ($email) use ($domain) {
            return preg_match("/^$domain\./", $email->getCode());
        });
    }
}
