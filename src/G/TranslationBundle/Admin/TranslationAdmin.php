<?php
namespace G\TranslationBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;


class TranslationAdmin extends Admin
{

    protected $baseRouteName = "tr";
    protected $baseRoutePattern = "tr/";


    protected function configureShowFields(ShowMapper $showMapper)
    {

    }

    protected function configureRoutes(RouteCollection $collection)
    {
        // parent::configureRoutes($collection);
        $collection->remove('export');
        $collection->add('grid', $this->getRouterIdParameter().'/tr/grid');
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
    }


    /**
     * Configure the list
     *
     * @param \Sonata\AdminBundle\Datagrid\ListMapper $list list
     */
    protected function configureListFields(ListMapper $list)
    {

    }
 
    /**
     * Configure the form
     *
     * @param FormMapper $formMapper formMapper
     */
    public function configureFormFields(FormMapper $formMapper)
    {
    }
}