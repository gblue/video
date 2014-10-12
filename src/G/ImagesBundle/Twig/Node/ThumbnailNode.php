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

class ThumbnailNode extends \Twig_Node
{
    protected $extensionName;

    /**
     * @param array                 $extensionName
     * @param string                $fileName
     * @param string                $app
     * @param string                $format
     * @param \Twig_Node_Expression $attributes
     * @param int                   $lineno
     * @param string                $tag
     */
    public function __construct($extensionName, $fileName, $app, $format, \Twig_Node_Expression $attributes, $lineno, $tag = null)
    {
        $this->extensionName = $extensionName;

        parent::__construct(array('image' => $fileName, 'app' => $app, 'format' => $format,'attributes' => $attributes), array(), $lineno, $tag);
    }

    /**
     * {@inheritdoc}
     */
    public function compile(\Twig_Compiler $compiler)
    {
        $compiler
            ->addDebugInfo($this)
            ->write(sprintf("echo \$this->env->getExtension('%s')->thumbnail(", $this->extensionName))
            ->subcompile($this->getNode('image'))
            ->raw(', ')
            ->subcompile($this->getNode('app'))
            ->raw(', ')
            ->subcompile($this->getNode('format'))
            ->raw(', ')
            ->subcompile($this->getNode('attributes'))
            ->raw(");\n")
        ;
    }
}
