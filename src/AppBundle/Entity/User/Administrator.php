<?php

namespace AppBundle\Entity\User;

use Smart\AuthenticationBundle\Entity\User\UserTrait;
use Smart\AuthenticationBundle\Security\LastLoginInterface;
use Smart\AuthenticationBundle\Security\SmartUserInterface;
use Smart\EtlBundle\Entity\ImportableTrait;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use Yokai\MessengerBundle\Recipient\SwiftmailerRecipientInterface;

/**
 * @ORM\Entity()
 * @ORM\Table(name="administrator")
 * @UniqueEntity(fields={"email"})
 */
class Administrator implements SmartUserInterface, \Serializable, SwiftmailerRecipientInterface, LastLoginInterface
{
    use UserTrait;
    use ImportableTrait;

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function __construct()
    {
        $this->setRoles(['ROLE_ADMINISTRATOR']);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getImportId()
    {
        return sprintf(
            '%s-%s',
            strtolower($this->getFirstName()),
            strtolower($this->getLastName())
        );
    }
}
