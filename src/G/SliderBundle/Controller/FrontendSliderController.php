<?php

namespace G\SliderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class FrontendSliderController extends Controller
{
    /**
     * @Template("GSliderBundle:Frontend:slider.html.twig")
     */
    public function sliderAction()
    {
    	$sliderManager = $this->getSliderManager();
    	$slides = $sliderManager->getAll('notHidden');
      return array(
      	'slides' => $slides
      );
    }

   	private function getSliderManager(){
   		$sliderManager = $this->container->get('manager.slider');
   		return $sliderManager;
   	}
}
