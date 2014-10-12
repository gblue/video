<?php

namespace G\SettingsBundle\Manager;

/**
 * Manager for settings.
 *
 * @author Nedelin Yordanov <https://github.com/Ch1pStar>
 **/
class SettingsManager{

    protected $em;


    public function __construct($em){
        $this->em = $em;

    }

    public function get($name){
    	$r = $this->em->getRepository('GSettingsBundle:Setting');
        $item = $r->findBy(array('name' => $name));
        
        if($item){   
            $item = $item[0];
            return $item->getValue();
        }
        return null;
    }
}
