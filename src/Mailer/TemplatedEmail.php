<?php

namespace App\Mailer;

/**
 * @author Mathieu Ducrot <mathieu.ducrot@smartbooster.io>
 */
class TemplatedEmail extends \Symfony\Bridge\Twig\Mime\TemplatedEmail
{
    /** @var string Unique email identifier */
    private string $code;

    /** @var array Used for subject parameters translation in AbstractMailer */
    private array $subjectParameters = [];

    public function __construct(string $code, string $locale)
    {
        parent::__construct();

        $this->code = $code;
        $this->subject("$code.subject");
        $this->htmlTemplate('email/' . str_replace('.', '/', $code) . ".$locale.html.twig");
    }

    public function code(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function subjectParameters(array $subjectParameters): self
    {
        $this->subjectParameters = $subjectParameters;

        return $this;
    }

    public function getSubjectParameters(): array
    {
        return $this->subjectParameters;
    }

    public function getDocumentationTitle(): string
    {
        return $this->getCode() . ".doc_title";
    }
}
