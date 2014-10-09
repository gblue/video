<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\Sonata\UserBundle\Admin\Entity;

use Sonata\UserBundle\Admin\Model\UserAdmin as BaseUserAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class UserAdmin extends BaseUserAdmin
{

	/**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $imageManager = $this->getConfigurationPool()->getContainer()->get('stenik_images.manager');
        // define group zoning
        $formMapper
            ->with('General', array('class' => 'col-md-6'))->end()
            ->with('Profile', array('class' => 'col-md-6'))->end()
            ->with('Management', array('class' => 'col-md-12'))->end()
        ;

        
        $formMapper
            ->with('General')
                ->add('username')
                ->add('email')
                ->add('plainPassword', 'text', array(
                    'required' => (!$this->getSubject() || is_null($this->getSubject()->getId()))
                ))
            ->end()
            ->with('Profile')
                ->add('firstname', null, array('required' => false))
                ->add('lastname', null, array('required' => false))
                ->add('city', 'text', array('required' => false))
            ->end()
        ;

        if ($this->getSubject() && !$this->getSubject()->hasRole('ROLE_SUPER_ADMIN')) {
            $formMapper
                ->with('Management')
                    ->add('groups', 'sonata_type_model', array(
                        'required' => false,
                        'expanded' => true,
                        'multiple' => true
                    ))
                    ->add('realRoles', 'sonata_security_roles', array(
                        'label'    => 'form.label_roles',
                        'expanded' => true,
                        'multiple' => true,
                        'required' => false
                    ))
                    ->add('locked', null, array('required' => false))
                    ->add('expired', null, array('required' => false))
                    ->add('enabled', null, array('required' => false))
                    ->add('credentialsExpired', null, array('required' => false))
                ->end()
            ;
        }
    }
    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection->remove('export');
    }

    public function prePersist($itm)
    {
        if($itm->getFile()) {
            $imageManager = $this->getConfigurationPool()->getContainer()->get('stenik_images.manager');
            $imageManager->deleteImageThumbnails('users', $itm->getPicture());

            $itm->upload();
            if($itm->getPicture())
                $imageManager->createImageThumbnails('users', $itm->getPicture());
        }
        
    }

    public function preUpdate($itm) 
    {
        if($itm->getFile()) {
            $imageManager = $this->getConfigurationPool()->getContainer()->get('stenik_images.manager');
            $imageManager->deleteImageThumbnails('users', $itm->getPicture());

            $itm->upload();
            if($itm->getPicture())
                $imageManager->createImageThumbnails('users', $itm->getPicture());
        }
        $em = $this->getModelManager()->getEntityManager($this->getClass());
        $original = $em->getUnitOfWork()->getOriginalEntityData($itm);
        $enabled = $original['enabled'];
        $settings = $this->getConfigurationPool()->getContainer()->get('settings_manager');
        $email = $itm->getEmail();
        $trans = $this->getTranslator();
        if ($enabled !== $itm->getIsEnabled()) {
            if ($enabled == true) {
                $message = \Swift_Message::newInstance()
                    ->setSubject($trans->trans('email.deactivation_title', array(), 'StenikFrontendBundle'))
                    ->setFrom(explode(',',$settings->get('contact_email')))
                    ->setTo($email)
                    ->setBody($trans->trans('email.account_deactivation_text', array(), 'StenikFrontendBundle'))
                ; 
                  $this->getConfigurationPool()->getContainer()->get('mailer')->send($message);
            }else{
                $message = \Swift_Message::newInstance()
                    ->setSubject($trans->trans('email.activation_title', array(), 'StenikFrontendBundle'))
                    ->setFrom(explode(',',$settings->get('contact_email')))
                    ->setTo($email)
                    ->setBody($trans->trans('email.account_activation_text', array(), 'StenikFrontendBundle'))
                ; 
                  $this->getConfigurationPool()->getContainer()->get('mailer')->send($message);
            }
        }        
    }


}
