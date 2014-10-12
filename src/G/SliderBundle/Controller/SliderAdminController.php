<?php

namespace G\SliderBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sonata\AdminBundle\Admin\FieldDescriptionCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Pix\SortableBehaviorBundle\Controller\SortableAdminController;

class SliderAdminController extends SortableAdminController
{
	public function historyAction($id = null)
    {
    	$this->admin->buildTabMenu('history');
    	$object = $this->admin->getObject($id);

    	$locale = $this->admin->getRequest()->query->get('locale', null);

		$em = $this->getDoctrine()->getEntityManager();
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

        return $this->render('GSliderBundle:Admin:history.html.twig', array(
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

    	$em = $this->getDoctrine()->getEntityManager();
    	$repo = $em->getRepository('Gedmo\Loggable\Entity\LogEntry'); // we use default log entry class
		if($locale !== null) {
			$translations = $object->getTranslations();
			$showObject = $translations[$locale];	
			$elements = array('title' => 'title', 'simple_description' => 'simple_description', 'description' => 'description');
		} else {
			$showObject = $object;
			$elements = array('slug' => 'slug', 'is_hidden' => 'is_hidden', 'updated_at' => 'updated_at', 'created_at' => 'created_at');
		}
		$repo->revert($showObject, $revision);


		$serializer = \JMS\Serializer\SerializerBuilder::create()->build();
		$jsonObject = $serializer->serialize($showObject, 'json');
		$objectToArray =  json_decode($jsonObject, true);


        return $this->render('GSliderBundle:Admin:show.html.twig', array(
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

    	$em = $this->getDoctrine()->getEntityManager();
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
		$em->persist($showObject);
		$em->flush();

		return new RedirectResponse($this->container->get('router')->generate('admin_stenik_slider_slider_edit', array('id' => $object->getId())));
    }
}
