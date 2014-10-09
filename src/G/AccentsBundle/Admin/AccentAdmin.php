<?php

namespace G\AccentsBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;

class AccentAdmin extends Admin
{

    protected function configureTabMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        parent::configureTabMenu($menu, $action, $childAdmin);
        $trans = $this->getTranslator();
        if($action == 'history') {
            $id = $this->getRequest()->get('id');
            $menu->addChild(
                $trans->trans("menu.item", array(), 'GAccentsBundle'),
                array('uri' => $this->generateUrl('history', array('id' => $id)))
            );

            $locales = $this->getConfigurationPool()->getContainer()->getParameter('locales');

            foreach ($locales as $value) {
                 $menu->addChild(
                    strtoupper($value),
                    array('uri' => $this->generateUrl('history', array('id' => $id, 'locale' => $value)))
                );
            }
        }
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection->add('history', $this->getRouterIdParameter().'/history');
        $collection->add('history_view_revision', $this->getRouterIdParameter().'/preview/{revision}');
        $collection->add('history_revert_to_revision', $this->getRouterIdParameter().'/revert/{revision}');
        
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('title', null, array('label' => 'form.title'))
            ->add('isHidden', null, array('label' => 'form.is_hidden'))
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('title', null, array('label' => 'form.title'))
            ->add('isHidden', null, array('label' => 'form.is_hidden', 'editable' => true))
            ->add('createdAt', null, array('label' => 'form.created_at'))
            ->add('_action', 'actions', array(
                'actions' => array(
                    'edit' => array(),
                    'delete' => array(),
                    'history' => array('template' => 'GAccentsBundle:Admin:list_action_history.html.twig'),
                ), 'label' => 'table.label_actions'
            ))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $trans = $this->getTranslator();
        $formMapper
            ->with('form.general', array('class' => 'col-md-12', 'label' => $trans->trans('form.general', array(), 'GAccentsBundle')))
                ->add('translations', 'a2lix_translations', array(
                    'fields' => array(
                        'title' => array(
                            'field_type' => 'text',
                            'label' => $trans->trans('form.title', array(), 'GAccentsBundle')
                        ),
                        'description' => array(
                            'field_type' => 'textarea',
                            'label' => $trans->trans('form.description', array(), 'GAccentsBundle'),
                            'required' => false,
                            'attr' => array(
                                'class' => 'tinymce',
                                'data-theme' => 'bbcode'
                            )
                        ),
                    ),
                    'label' => $trans->trans('form.translations', array(), 'GAccentsBundle')
                ))
            ->end()
            ->with('form.more', array('class' => 'col-md-12', 'label' => $trans->trans('form.general', array(), 'GAccentsBundle')))
                ->add('image', 'sonata_type_model_list', array('required' => false, 'label' => 'form.label_image'), array(
                    'link_parameters' => array(
                        'context' => 'accents'
                    ), 
                ))
                ->add('url',null,  array('label' => 'form.url', 'required' => false))
                ->add('is_hidden', 'checkbox', array('label' => 'form.is_hidden', 'required' => false))
            ->end();
    }
}
