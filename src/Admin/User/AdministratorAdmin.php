<?php

namespace App\Admin\User;

use App\Entity\User\Administrator;
use Smart\SonataBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

/**
 * @author Nicolas Bastien <nicolas.bastien@smartbooster.io>
 */
class AdministratorAdmin extends AbstractAdmin
{
    protected function configureRoutes(RouteCollection $collection): void
    {
        $collection->add('sendAccountCreationEmail', $this->getRouterIdParameter() . '/sendAccountCreationEmail');
    }

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
            /** @var Administrator $currentUser */
            $currentUser = $this->getUser();
            if ($name === static::ACTION_DELETE && $object->getId() === $currentUser->getId()) {
                return false;
            }
        }

        return parent::isGranted($name, $object);
    }

    /**
     * @param DatagridMapper $filter
     *
     * @return void
     */
    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter
            ->add('id', null, ['label' => 'list.label_id'])
            ->add('email', null, [
                'show_filter' => true,
                'label' => 'form.label_email'
            ])
            ->add('lastName', null, [
                'show_filter' => true,
                'label' => 'form.label_last_name'
            ]);
    }

    /**
     * @return void
     */
    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('id')
            ->addIdentifier('email', null, ['label' => 'form.label_email'])
            ->add('firstName', null, ['label' => 'form.label_first_name'])
            ->add('lastName', null, ['label' => 'form.label_last_name'])
            ->add('lastLogin', null, ['label' => 'form.label_last_login'])
        ;
    }

    /**
     * @return void
     */
    protected function configureShowFields(ShowMapper $show)
    {
        $show
            ->with('fieldset.label_general')
                ->add('email', null, ['label' => 'form.label_email'])
                ->add('firstName', null, ['label' => 'form.label_first_name'])
                ->add('lastName', null, ['label' => 'form.label_last_name'])
                ->add('lastLogin', null, ['label' => 'form.label_last_login'])
            ->end()
        ;
    }

    /**
     * @return void
     */
    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->with('fieldset.label_general', ['class' => 'col-md-7'])
                ->add('email', null, ['label' => 'form.label_email'])
                ->add('firstName', null, ['label' => 'form.label_first_name'])
                ->add('lastName', null, ['label' => 'form.label_last_name'])
            ->end()
            ->with('fieldset.label_password', ['class' => 'col-md-5 pull-right'])
                ->add(
                    'plainPassword',
                    RepeatedType::class,
                    [
                        'type'               => PasswordType::class,
                        'required'           => $this->isNew(),
                        'first_options'      => ['label' => 'form.label_password'],
                        'second_options'     => ['label' => 'form.label_password_confirmation'],
                        'options' => array('translation_domain' => 'admin'),
                        'invalid_message' => 'reset_password.password_must_match',
                    ]
                )
            ->end()
        ;
    }
}
