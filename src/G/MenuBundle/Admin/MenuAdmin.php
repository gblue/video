<?php

namespace G\MenuBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;

class MenuAdmin extends Admin
{

    protected function configureTabMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        parent::configureTabMenu($menu, $action, $childAdmin);
        $trans = $this->getTranslator();
        if($action == 'history') {
            $id = $this->getRequest()->get('id');
            $menu->addChild(
                $trans->trans("menu.item", array(), 'GMenuBundle'),
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
        $collection->remove('export');
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
                    'history' => array('template' => 'GMenuBundle:Admin:list_action_history.html.twig'),
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
        $choices = array('_self' => $trans->trans('self', array(), 'GMenuBundle'), '_blank' =>  $trans->trans('blank', array(), 'GMenuBundle'));
        
        $formMapper
            ->with('form.general', array('class' => 'col-md-7', 'label' => $trans->trans('form.general', array(), 'GMenuBundle')))
                ->add('translations', 'a2lix_translations', array(
                    'fields' => array(
                        'title' => array(
                            'field_type' => 'text',
                            'label' => $trans->trans('form.title', array(), 'GMenuBundle')
                        ),
                        'link' => array(
                            'field_type' => 'text',
                            'label' => $trans->trans('form.link', array(), 'GMenuBundle')
                        ),
                        'target' => array(
                            'field_type' => 'choice',
                            'label' => $trans->trans('form.target', array(), 'GMenuBundle'),
                            'choices' => $choices
                        ),
                    ),
                    'label' => $trans->trans('form.translations', array(), 'GMenuBundle')
                ))
            ->end()
            ->with('form.more', array('class' => 'col-md-5', 'label' => $trans->trans('form.general', array(), 'GMenuBundle')))
                ->add('parent', 'sonata_type_model_list', array('required' => false, 'label' => $trans->trans('form.parent', array(), 'GMenuBundle')), array(
                    'link_parameters' => array(
                        'context' => 'menu'
                    )
                ))
                ->add('rank', 'text', array('label' => 'form.rank', 'required' => false))
                ->add('isHidden', 'checkbox', array('label' => 'form.isHidden', 'required' => false))
            ->end();
    }
}
