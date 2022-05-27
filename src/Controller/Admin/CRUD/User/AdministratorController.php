<?php

namespace App\Controller\Admin\CRUD\User;

use Smart\AuthenticationBundle\Controller\CRUD\SendAccountCreationEmailTrait;
use Sonata\AdminBundle\Controller\CRUDController;

class AdministratorController extends CRUDController
{
    use SendAccountCreationEmailTrait;
}
