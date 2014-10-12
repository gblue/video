<?php

namespace  G\SettingsBundle\Controller;


use Sonata\AdminBundle\Controller\CRUDController as Controller;

class CRUDController extends Controller{

 

	public function listAction(){
		if (false === $this->admin->isGranted('LIST')) {
            throw new AccessDeniedException();
        }

        $datagrid = $this->admin->getDatagrid();
        $formView = $datagrid->getForm()->createView();

        // set the theme for the current Admin Form
        $this->get('twig')->getExtension('form')->renderer->setTheme($formView, $this->admin->getFilterTheme());
        

        return $this->render('GSettingsBundle:Admin:view.html.twig', array(
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