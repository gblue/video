<?php
namespace G\ContentBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;


class ContentAdmin extends Admin
{

    protected function configureTabMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        parent::configureTabMenu($menu, $action, $childAdmin);
        $trans = $this->getTranslator();
        if($action == 'history') {
            $id = $this->getRequest()->get('id');
            $menu->addChild(
                $trans->trans("menu.item", array(), 'GContentBundle'),
                array('uri' => $this->generateUrl('history', array('id' => $id)))
            );

            $locales = $this->getConfigurationPool()->getContainer()->getParameter('locales');

            foreach ($locales as $value) {
                 $menu->addChild(
                    strtoupper($value),
                    array('uri' => $this->generateUrl('history', array('id' => $id, 'locale' => $value)))
                );
            }
        } else if ($action == 'tree' || $action == 'list') {
            $menu->addChild(
                $trans->trans("menu.list", array(), 'GContentBundle'),
                array('uri' => $this->generateUrl('list'))
            );
            $menu->addChild(
                $trans->trans("menu.tree", array(), 'GContentBundle'),
                array('uri' => $this->generateUrl('tree'))
            );
        }
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection->add('history', $this->getRouterIdParameter().'/history');
        $collection->add('tree', 'tree');
        $collection->add('history_view_revision', $this->getRouterIdParameter().'/preview/{revision}');
        $collection->add('history_revert_to_revision', $this->getRouterIdParameter().'/revert/{revision}');
    }


    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title', null, array('label' => 'form.title'))
            ->add('description', null, array('label' => 'form.description'))
        ;
    }


    /**
     * Configure the list
     *
     * @param \Sonata\AdminBundle\Datagrid\ListMapper $list list
     */
    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('title', null, array('label' => 'form.title'))
            ->add('lft', 'string', array('label' => 'form.path',  'template' => 'GContentBundle:Admin:list_parent.html.twig'))
            ->add('is_hidden', null, array('label' => 'form.is_hidden', 'editable' => true))
            ->add('is_visible', null, array('label' => 'form.is_visible', 'editable' => true))
            ->add('_action', 'actions', array(
                    'actions' => array(
                        'edit' => array(),
                        'delete' => array(),
                        'history' => array('template' => 'GContentBundle:Admin:list_action_history.html.twig'),
                    ),
                    'label' => 'form.label_actions'
                ))
            ;
    }
 
    /**
     * Configure the form
     *
     * @param FormMapper $formMapper formMapper
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        $trans = $this->getTranslator();
        $formMapper
            ->with('form.general', array('class' => 'col-md-12', 'label' => $trans->trans('form.general', array(), 'GContentBundle')))
                ->add('parent', 'sonata_type_model_list', array('required' => false, 'label' => $trans->trans('form.parent', array(), 'GContentBundle')), array(
                    'link_parameters' => array(
                        'context' => 'content'
                    )
                ))
                ->add('translations', 'a2lix_translations', array(
                    'fields' => array(
                        'slug' => array(
                            'field_type' => 'text',
                            'label' => $trans->trans('form.slug', array(), 'GContentBundle')
                        ),
                        'title' => array(
                            'field_type' => 'text',
                            'label' => $trans->trans('form.title', array(), 'GContentBundle')
                        ),
                        'description' => array(
                            'field_type' => 'textarea',
                            'label' => $trans->trans('form.description', array(), 'GContentBundle'),
                            'required' => false,
                            'attr' => array(
                                'class' => 'tinymce',
                            )
                        ),
                        'meta_title' => array(
                            'required' => false,
                            'label' => $trans->trans('form.meta_title', array(), 'GContentBundle')
                        ),
                        'meta_description' => array(
                            'required' => false,
                            'label' => $trans->trans('form.meta_description', array(), 'GContentBundle')
                        ),
                        'meta_keywords' => array(
                            'required' => false,
                            'label' => $trans->trans('form.meta_keywords', array(), 'GContentBundle')
                        ),
                    ),
                    'label' => $trans->trans('form.translations', array(), 'GContentBundle')
                ))
            ->end()
            ->with('form.more', array('class' => 'col-md-12', 'label' => $trans->trans('form.general', array(), 'GContentBundle')))
                ->add('is_hidden', 'checkbox', array('label' => 'form.is_hidden', 'required' => false))
                ->add('is_system', 'checkbox', array('label' => 'form.is_system', 'required' => false))
                //->add('is_visible', 'checkbox', array('label' => 'form.is_visible', 'required' => false))
            ->end();
    }
}