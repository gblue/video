<?php 

namespace Stenik\ImagesBundle\Twig\Extensions;


use Stenik\ImagesBundle\Twig\TokenParser\ThumbnailTokenParser;
use Stenik\ImagesBundle\Twig\TokenParser\WebPathTokenParser;

/**
* StenikImagesExtension
* @author Hristo Hristoff <hristo.hristov@stenik.bg>
*/
class StenikImagesExtension extends \Twig_Extension
{

	private $manager;

	public function __construct($imagesManager)
	{
		$this->manager = $imagesManager;
	}

    /**
     * {@inheritdoc}
     */
    public function getTokenParsers()
    {
        return array(
            new ThumbnailTokenParser($this->getName()),
            new WebPathTokenParser($this->getName()),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * @param string $template
     * @param array  $parameters
     *
     * @return mixed
     */
    public function render($template, array $parameters = array())
    {
        if (!isset($this->resources[$template])) {
            $this->resources[$template] = $this->environment->loadTemplate($template);
        }

        return $this->resources[$template]->render($parameters);
    }

    public function thumbnail($image, $app, $format, $options = array())
    {
        if(strlen($image) == 0) {
            return '';
        }
		$arguments = '';
		foreach ($options as $key => $value) {
			$arguments .= ' '.$key.'="'.$value.'"';
		}
		return '<img src="'.$this->manager->getImageURL($app, $format) . $image.'"'.$arguments.'/>';
    }

    public function webpath($image, $app, $format)
    {
        if(strlen($image) == 0) {
            return '';
        }
		return $this->manager->getImageURL($app, $format) . $image;
    }

    public function getName()
    {
        return 'stenik_images';
    }
}