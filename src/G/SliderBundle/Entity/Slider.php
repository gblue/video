<?php
namespace G\SliderBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Translatable\TranslationInterface as Translatable;

/**
 * @ORM\Table(name="slider")
 * @ORM\Entity
 * @Gedmo\Loggable
 */
class Slider
{
    use \A2lix\TranslationFormBundle\Util\Gedmo\GedmoTranslatable;

    /** @ORM\Id @ORM\GeneratedValue @ORM\Column(type="integer") */
    protected $id;


    /**
     * @Gedmo\Versioned
     * @Gedmo\Translatable
     * @ORM\Column(name="title", type="string", length=255)
     */
    protected $title;

    /**
     * @Gedmo\Versioned
     * @Gedmo\Translatable
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     */
    protected $url;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(name="target", type="string", length=255, nullable=true)
     */
    protected $target = '_self';

    /**
     * @Gedmo\Versioned
     * @Gedmo\Translatable
     * @ORM\Column(name="alt", type="string", length=255, nullable=true)
     */
    protected $alt;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(name="is_hidden", type="boolean", options={"default" = 0})
     */
    protected $is_hidden;

    /**
     * @Gedmo\Versioned
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $created_at;

    /**
     * @Gedmo\Versioned
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime")
     */
    protected $updated_at;

    /**
     * @Gedmo\Sortable
     * @ORM\Column(name="position", type="integer")
     */
    private $position;

    /** 
     * @ORM\OneToMany(targetEntity="G\SliderBundle\Entity\SliderTranslation", mappedBy="object", cascade={"persist", "remove"}, indexBy="locale")
     */
    protected $translations;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($value)
    {
        $this->title = $value;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($value)
    {
        $this->url = $value;
    }

    public function getTarget()
    {
        return $this->target;
    }

    public function setTarget($value)
    {
        $this->target = $value;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($value)
    {
        $this->description = $value;
    }

    public function getAlt()
    {
        return $this->alt;
    }

    public function setAlt($value)
    {
        $this->alt = $value;
    }

    public function getIsHidden()
    {
        return $this->is_hidden;
    }

    public function setIsHidden($is_hidden)
    {
        $this->is_hidden = $is_hidden;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }

    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;
    }

    /**
     * Set position
     *
     * @param string $position
     * @return Partner
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return string 
     */
    public function getPosition()
    {
        return $this->position;
    }


    public function __toString()
    {
        return $this->getTitle() ?: 'n/a';
    }
}