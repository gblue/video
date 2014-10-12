<?php

namespace G\LayoutBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class LayoutController extends Controller
{
    /**
     * @Route("/")
     * @Template("GLayoutBundle:Home:index.html.twig")
     */
    public function indexAction()
    {
        $contentManager = $this->getManager('content');
        $content = $contentManager->getBySlug('our-mission');
        $accentsManager = $this->getManager('accents');
        $accents = $accentsManager->getAll();
        return array(
            'accents' => $accents,
            'content' => $content
        );
    }

    /**
     * @Template("GLayoutBundle:Home:mini_menu.html.twig")
     */
    public function miniMenuAction()
    {
    	return array();
    }
    /**
     * @Template("GLayoutBundle:Home:contacts.html.twig")
     */
    public function contactsAction()
    {
        $settingsManager = $this->get('settings_manager');
        $fb = $settingsManager->get('fb');
        $gp = $settingsManager->get('gp');
        $twitter = $settingsManager->get('twitter');
        $youtube = $settingsManager->get('youtube');
        return array(
            'fb' => $fb,
            'gp' => $gp,
            'twitter' => $twitter,
            'youtube' => $youtube
        );
    }

    private function getManager($class){
        $service = 'manager.'.$class.'';
        return $this->container->get($service);
    }
}
