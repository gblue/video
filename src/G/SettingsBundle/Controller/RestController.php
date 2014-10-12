<?php

namespace G\SettingsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\JsonResponse;

class RestController extends Controller{


    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function listAction(){

        $em = $this->getDoctrine()->getManager();
        $r = $em->getRepository('GSettingsBundle:Setting');
        $list = $r->findAll();

        $arrList = array();
        foreach ($list as $key => $value) {
            $arrList[] = $value->toArray() + array('delete' => '<a href="">delete</a>');
        }
        $c = 1;

        return new JsonResponse(array(
            'settings' => $arrList,
            'total' => $c,
        ));  
    }

    /**
     * @throws NotFoundHttpException
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function updateAction($id){
        $request = $this->getRequest();

        if (!$request->isMethod('PUT')) {
            throw $this->createNotFoundException('Invalid request method.');
        }

        $em = $this->getDoctrine()->getManager();
        $r = $em->getRepository('GSettingsBundle:Setting');
        $item = $r->find($id);

        $item->setLabel($request->get('label'));
        $item->setName($request->get('name'));
        $item->setValue($request->get('value'));
        $item->setVisible($request->get('visible'));

        $em->persist($item);
        $em->flush(); 

        return new JsonResponse($item->toArray()); 
    }

}
