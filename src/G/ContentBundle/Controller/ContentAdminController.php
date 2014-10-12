<?php

namespace G\ContentBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sonata\AdminBundle\Admin\FieldDescriptionCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sonata\AdminBundle\Controller\CRUDController;

class ContentAdminController extends CRUDController
{

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function treeAction()
    {
        $this->admin->buildTabMenu('tree');
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('G\ContentBundle\Entity\Content');
        $options = array(
            'decorate' => true,
            'rootOpen' => '<ul class="page-tree">',
            'rootClose' => '</ul>',
            'childOpen' => '<li>',
            'childClose' => '</li>',
            'nodeDecorator' => function($node) {
                $em = $this->getDoctrine()->getManager();
                $repo = $em->getRepository('G\ContentBundle\Entity\Content');
                $a = $repo->findOneBy(array('id' => $node['id']));
                $url = $this->container->get('router')->generate('admin_content_edit', array('id' => $node['id']));
                $title = $a->getTitle();
                $str = <<<CODE
                <div class="page-tree__item">
                    <i class="fa fa-caret-right"></i>
                    <a class="page-tree__item__edit" href="{$url}">{$title}</a>
                </div>
CODE;
                return $str;
            }
        );
        $htmlTree = $repo->childrenHierarchy(
            null, /* starting from root nodes */
            false, /* true: load all children, false: only direct */
            $options
        );
        
        $datagrid = $this->admin->getDatagrid();
        $formView = $datagrid->getForm()->createView();

        $this->get('twig')->getExtension('form')->renderer->setTheme($formView, $this->admin->getFilterTheme());

        return $this->render('GContentBundle:Admin:tree.html.twig', array(
            'action'      => 'tree',
            'tree'       => $htmlTree,
            'form'        => $formView,
            'csrf_token'  => $this->getCsrfToken('sonata.batch'),
        ));
    }


    public function historyAction($id = null)
    {
    	$this->admin->buildTabMenu('history');
    	$object = $this->admin->getObject($id);

    	$locale = $this->admin->getRequest()->query->get('locale', null);

		$em = $this->getDoctrine()->getManager();
    	$repo = $em->getRepository('Gedmo\Loggable\Entity\LogEntry'); // we use default log entry class
    	if($locale !== null) {
    		$translations = $object->getTranslations();
    		if($translations[$locale] !== null) {
    			$logs = $repo->getLogEntries($translations[$locale]);	
    		} else {
    			$logs = array();
    		}
    	} else {
    		$logs = $repo->getLogEntries($object);
    	}

        return $this->render('GContentBundle:Admin:history.html.twig', array(
            'object' => $object,
            'revisions' => $logs,
            'action' => 'history',
            'locale' => $locale
        ));
    }

    public function historyViewRevisionAction($id = null, $revision = null)
    {
    	$object = $this->admin->getObject($id);
    	$locale = $this->admin->getRequest()->query->get('locale', null);

    	$em = $this->getDoctrine()->getManager();
    	$repo = $em->getRepository('Gedmo\Loggable\Entity\LogEntry'); // we use default log entry class
		if($locale !== null) {
			$translations = $object->getTranslations();
			$showObject = $translations[$locale];	
			$elements = array(
                'title' => 'title', 
                'description' => 'description', 
                'meta_title' => 'meta_title', 
                'meta_description' => 'meta_description', 
                'meta_keywords' => 'meta_keywords'
            );
		} else {
			$showObject = $object;
			$elements = array('slug' => 'slug', 'is_hidden' => 'is_hidden', 'updated_at' => 'updated_at', 'created_at' => 'created_at');
		}
		$repo->revert($showObject, $revision);


		$serializer = \JMS\Serializer\SerializerBuilder::create()->build();
		$jsonObject = $serializer->serialize($showObject, 'json');
		$objectToArray =  json_decode($jsonObject, true);


        return $this->render('GContentBundle:Admin:show.html.twig', array(
            'object' => $object,
            'objectToArray' => $objectToArray,
            'action' => 'show',
            'locale' => $locale,
            'elements' => $elements,
            'revision' => $revision
        ));
    }

    public function historyRevertToRevisionAction($id = null, $revision = null)
    {
    	$object = $this->admin->getObject($id);
    	$locale = $this->admin->getRequest()->query->get('locale', null);

    	$em = $this->getDoctrine()->getManager();
    	$repo = $em->getRepository('Gedmo\Loggable\Entity\LogEntry'); // we use default log entry class
		if($locale !== null) {
			$translations = $object->getTranslations();
			$showObject = $translations[$locale];	
			$elements = array('title' => 'title', 'description' => 'description');
		} else {
			$showObject = $object;
			$elements = array('slug' => 'slug', 'is_hidden' => 'is_hidden', 'updated_at' => 'updated_at', 'created_at' => 'created_at');
		}
		$repo->revert($showObject, $revision);
		$em->persist($showObject);
		$em->flush();

		return new RedirectResponse($this->container->get('router')->generate('admin_content_edit', array('id' => $object->getId())));
    }

}
