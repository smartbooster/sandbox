<?php

namespace AppBundle\Admin\User;

use AppBundle\Entity\User\Administrator;
use Smart\AuthenticationBundle\Security\Token;
use Smart\SonataBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Yokai\MessengerBundle\Sender\SenderInterface;
use Yokai\SecurityTokenBundle\Manager\TokenManagerInterface;

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
            ->add('lastLogin', null, ['label' => 'form.label_last_login'])
        ;
    }

    /**
     * @inheritDoc
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
     * @inheritDoc
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
                    'repeated',
                    [
                        'type'               => 'password',
                        'required'           => $this->isNew(),
                        'first_options'      => ['label' => 'form.label_password'],
                        'second_options'     => ['label' => 'form.label_password_confirmation'],
                        'options' => array('translation_domain' => 'admin'),
                    ]
                )
            ->end()
        ;
    }

    /**
     * @param Administrator $object
     */
    public function postPersist($object)
    {
        $token = $this->getTokenManager()->create(Token::RESET_PASSWORD, $object);

        $this->getMessenger()->send(
            'security.user_created',
            $object,
            [
                '{context}' => 'admin',
                'token' => $token->getValue(),
                'domain' => $this->getConfigurationPool()->getContainer()->getParameter('domain'),
                'security_reset_password_route' => 'admin_security_reset_password'
            ]
        );
    }

    /**
     * @return TokenManagerInterface
     */
    private function getTokenManager()
    {
        return $this->get('yokai_security_token.token_manager');
    }

    /**
     * @return SenderInterface
     */
    protected function getMessenger()
    {
        return $this->get('yokai_messenger.sender');
    }
}
