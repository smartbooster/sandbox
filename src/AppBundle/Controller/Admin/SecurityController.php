<?php

namespace AppBundle\Controller\Admin;

use Smart\AuthenticationBundle\Controller\AbstractSecurityController;

/**
 * @author Nicolas Bastien <nicolas.bastien@smartbooster.io>
 */
class SecurityController extends AbstractSecurityController
{
    protected $context = 'admin';
}
