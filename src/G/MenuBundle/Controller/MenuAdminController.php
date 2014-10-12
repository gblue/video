<?php

namespace G\MenuBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;


class MenuAdminController extends CRUDController
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

        return $this->render('GMenuBundle:Admin:history.html.twig', array(
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
            $elements = array('title' => 'title', 'link' => 'link');
        } else {
            $showObject = $object;
            $elements = array('title' => 'title', 'isHidden' => 'isHidden', 'updatedAt' => 'updatedAt', 'createdAt' => 'createdAt');
        }
        $repo->revert($showObject, $revision);


        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        $jsonObject = $serializer->serialize($showObject, 'json');
        $objectToArray =  json_decode($jsonObject, true);


        return $this->render('GMenuBundle:Admin:show.html.twig', array(
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
                'link' => 'link'
            );
        } else {
            $showObject = $object;
            $elements = array('title' => 'title', 'isHidden' => 'isHidden', 'updatedAt' => 'updatedAt', 'createdAt' => 'createdAt');
        }
        $repo->revert($showObject, $revision);
        $em->persist($showObject);
        $em->flush();

        return new RedirectResponse($this->container->get('router')->generate('admin_g_menu_menu_edit', array('id' => $object->getId())));
    }
}