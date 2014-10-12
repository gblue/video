<?php
namespace G\SliderBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;


class SliderAdmin extends Admin
{
     public $last_position = 0;

     private $container;
     private $positionService;

     public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container)
     {
         $this->container = $container;
     }

     public function setPositionService(\Pix\SortableBehaviorBundle\Services\PositionHandler $positionHandler)
     {
         $this->positionService = $positionHandler;
     }

     protected $datagridValues = array(
         '_page' => 1,
         '_sort_order' => 'ASC',
         '_sort_by' => 'position',
     );

    protected function configureTabMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        parent::configureTabMenu($menu, $action, $childAdmin);

        if($action == 'history') {
            $id = $this->getRequest()->get('id');
            $menu->addChild(
                "General",
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
        $collection->remove('show');
        $collection->remove('export');
        $collection->add('history', $this->getRouterIdParameter().'/history');
        $collection->add('history_view_revision', $this->getRouterIdParameter().'/preview/{revision}');
        $collection->add('history_revert_to_revision', $this->getRouterIdParameter().'/revert/{revision}');
        $collection->add('move', $this->getRouterIdParameter() . '/move/{position}');
        
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title', null, array('form.title'))
        ;
    }


    /**
     * Configure the list
     *
     * @param \Sonata\AdminBundle\Datagrid\ListMapper $list list
     */
    protected function configureListFields(ListMapper $list)
    {
        $this->last_position = $this->positionService->getLastPosition($this->getRoot()->getClass());
        $list
            ->add('custom', 'string', array('template' => 'GSliderBundle:Admin:list_slider_custom.html.twig', 'label' => 'form.custom'))
            ->add('is_hidden', null, array('label' => 'form.is_hidden'))
            ->add('created_at', null, array('label' => 'form.created_at'))
            ->add('_action', 'actions', array(
                    'actions' => array(
                        'edit' => array(),
                        'delete' => array(),
                        'history' => array('template' => 'GSliderBundle:Admin:list_action_history.html.twig', 'label' => 'action_history'),
                        'move' => array('template' => 'PixSortableBehaviorBundle:Default:_sort.html.twig'),
                    ),
                    'label' => 'actions'
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
        $formMapper
            ->with('General')
                ->add('translations', 'a2lix_translations', array(
                    'fields' => array(
                        'title' => array(
                            'field_type' => 'text',
                            'label' => 'form.title'
                        ),
                        'description' => array(
                            'field_type' => 'textarea',
                            'required' => false,
                            'attr' => array(
                                'class' => 'tinymce',
                            ),
                            'label' => 'form.description'
                        ),
                        'alt' => array(
                            'required' => false,
                            'label' => 'form.alt'
                        ),
                        'file' => array( 
                            'field_type' => 'sonata_media_type', 
                            'provider' => 'sonata.media.provider.image',
                            'context'  => 'slider',
                            'data_class'   =>  'Application\Sonata\MediaBundle\Entity\Media',
                            'required' => false,
                            'empty_on_new'  => true,
                            'new_on_update' => false,

                            'label' => 'form.file'
                        )
                    ),
                    'label' => 'form.translations',
                    'translation_domain' => 'GSliderBundle',
                ))
                ->add('url', 'text', array(
                    'required' => false,
                    'label' => 'form.url'
                ))
                ->add('target', 'choice', array(
                    'choices' => array(
                        '_self' => 'В същата страница',
                        '_blank' => 'В нова страница'
                    ),
                    'required' => false,
                    'label' => 'form.is_hidden'
                ))
                ->add('is_hidden', 'checkbox', array(
                    'required' => false,
                    'label' => 'form.is_hidden'
                ))
            ->end();
    }
}