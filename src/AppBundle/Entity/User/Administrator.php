<?php

namespace AppBundle\Entity\User;

use Smart\AuthenticationBundle\Entity\User\UserTrait;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="administrator")
 * @UniqueEntity(fields={"email"})
 */
class Administrator implements UserInterface, \Serializable
{
    use UserTrait;

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
}
