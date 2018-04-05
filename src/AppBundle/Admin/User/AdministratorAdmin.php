<?php

namespace AppBundle\Admin\User;

use AppBundle\Entity\User\Administrator;
use Smart\SonataBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;

/**
 * @author Nicolas Bastien <nicolas.bastien@smartbooster.io>
 */
class AdministratorAdmin extends AbstractAdmin
{
    /**
     * {@inheritdoc}
     */
    public function isGranted($name, $object = null)
    {
        if (!parent::isGranted($name, $object)) {
            return false;
        }

        if ($object instanceof Administrator) {
            // an ADMIN cannot delete himself
            if ($name === static::ACTION_DELETE && $object->getId() === $this->getUser()->getId()) {
                return false;
            }
        }

        return parent::isGranted($name, $object);
    }

    /**
     * @inheritdoc
     */
    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('id')
            ->addIdentifier('email', null, ['label' => 'form.label_email'])
            ->add('firstName', null, ['label' => 'form.label_first_name'])
            ->add('lastName', null, ['label' => 'form.label_last_name'])
        ;
    }
}
