<?php

namespace G\SettingsBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class SettingAdmin extends Admin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {

        $options = array();
         if ($this->id($this->getSubject())) {
            $options['read_only'] = true;
          }
          $options['label'] = 'form.label_name';
        $formMapper
            ->add('name', 'text', $options)
            ->add('value', null, array('label' => 'form.label_value'))
            ->add('label', null, array('label' => 'form.label_label'))
            ->add('visible', null, array('label' => 'form.label_visible'))
        ;
    }
    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection->remove('export');
    }
}