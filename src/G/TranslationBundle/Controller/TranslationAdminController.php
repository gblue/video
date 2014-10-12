<?php

namespace G\TranslationBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sonata\AdminBundle\Admin\FieldDescriptionCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\HttpFoundation\RedirectResponse;

class TranslationAdminController extends CRUDController
{

    public function listAction(){

        return $this->render('GTranslationBundle:Admin:view.html.twig', array(
            'layout'    => $this->container->getParameter('lexik_translation.base_layout'),
            'inputType' => $this->container->getParameter('lexik_translation.grid_input_type'),
            'locales'   => $this->getManagedLocales(),
        ));

    }

    
    /**
     * Returns managed locales.
     *
     * @return array
     */
    protected function getManagedLocales()
    {
        return $this->container->getParameter('lexik_translation.managed_locales');
    }
}
