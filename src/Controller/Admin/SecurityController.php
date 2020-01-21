<?php

namespace App\Controller\Admin;

use Smart\AuthenticationBundle\Controller\AbstractSecurityController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Nicolas Bastien <nicolas.bastien@smartbooster.io>
 */
class SecurityController extends AbstractSecurityController
{
    protected $context = 'admin';
}
