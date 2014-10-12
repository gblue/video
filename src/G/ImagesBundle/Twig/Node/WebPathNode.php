<?php

/*
 * This file is part of sonata-project.
 *
 * (c) 2010 Thomas Rabaix
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Stenik\ImagesBundle\Twig\Node;

class WebPathNode extends \Twig_Node
{
    protected $extensionName;

    /**
     * @param array                 $extensionName
     * @param \Twig_Node_Expression $image
     * @param \Twig_Node_Expression $app
     * @param \Twig_Node_Expression $format
     * @param integer               $lineno
     * @param string                $tag
     */
    public function __construct($extensionName, \Twig_Node_Expression $image,  \Twig_Node_Expression $app, \Twig_Node_Expression $format, $lineno, $tag = null)
    {
        $this->extensionName = $extensionName;

        parent::__construct(array('image' => $image, 'app' => $app, 'format' => $format), array(), $lineno, $tag);
    }

    /**
     * {@inheritdoc}
     */
    public function compile(\Twig_Compiler $compiler)
    {
        $compiler
            ->addDebugInfo($this)
            ->write(sprintf("echo \$this->env->getExtension('%s')->webpath(", $this->extensionName))
            ->subcompile($this->getNode('image'))
            ->raw(', ')
            ->subcompile($this->getNode('app'))
            ->raw(', ')
            ->subcompile($this->getNode('format'))
            ->raw(");\n")
        ;
    }
}