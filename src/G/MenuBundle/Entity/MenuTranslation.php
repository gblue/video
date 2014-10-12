<?php 

namespace G\MenuBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractTranslation;

/**
 * G\MenuBundle\Entity\MenuTranslation.php
 *
 * @ORM\Entity
 * @ORM\Table(name="menu_i18n", uniqueConstraints={@ORM\UniqueConstraint(name="lookup_unique_idx", columns={
 *     "locale", "object_id"
 *   })}
 * )
 * @Gedmo\Loggable
 */
class MenuTranslation extends AbstractTranslation
{

    /**
     * @ORM\ManyToOne(targetEntity="G\MenuBundle\Entity\Menu", inversedBy="translations")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $object;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(length=255, nullable=true)
     */
    private $title;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(type="text", nullable=true)
     */
    private $link;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(length=255, nullable=true)
     */
    private $target;

    /**
     * Convinient constructor
     *
     * @param string $locale
     * @param string $title
     * @param string $link
     * @param string $target
     */
    public function __construct($locale = null, $title = null, $link = null, $target = null)
    {
        $this->locale = $locale;
        $this->title = $title;
        $this->link = $link;
        $this->target = $target;
    }

    public function setLink($link)
    {
        $this->link = $link;
        return $this;
    }

    public function getLink()
    {
        return $this->link;
    }

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Gets the value of target.
     *
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Sets the value of target.
     *
     * @param string $target the target
     *
     * @return self
     */
    public function setTarget($target)
    {
        $this->target = $target;

        return $this;
    }
}