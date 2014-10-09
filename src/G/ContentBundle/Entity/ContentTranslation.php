<?php 

namespace G\ContentBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractTranslation;

/**
 * G\ContentBundle\Entity\ContentTranslation.php
 *
 * @ORM\Entity
 * @ORM\Table(name="content_i18n", uniqueConstraints={@ORM\UniqueConstraint(name="lookup_unique_idx", columns={
 *     "locale", "object_id"
 *   }), @ORM\UniqueConstraint(name="slug_unique_idx", columns={"slug"})}
 * )
 * @Gedmo\Loggable
 */
class ContentTranslation extends AbstractTranslation
{

    /**
     * @ORM\ManyToOne(targetEntity="G\ContentBundle\Entity\Content", inversedBy="translations")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $object;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(length=255, nullable=true)
     */
    private $title;

    /**
    * @Gedmo\Slug(fields={"title"}, separator="-", updatable=false)
    * @ORM\Column(length=255, unique=true)
    */
    private $slug;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(length=255, nullable=true)
     */
    protected $meta_title;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(type="text", nullable=true)
     */
    protected $meta_description;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(type="text", nullable=true)
     */
    protected $meta_keywords;

    /**
     * Convinient constructor
     *
     * @param string $locale
     * @param string $title
     * @param string $description
     */
    public function __construct($locale = null, $title = null, $description = null)
    {
        $this->locale = $locale;
        $this->title = $title;
        $this->description = $description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setMetaTitle($meta_title)
    {
        $this->meta_title = $meta_title;
    }

    public function getMetaTitle()
    {
        return $this->meta_title;
    }

    public function setMetaDescription($meta_description)
    {
        $this->meta_description = $meta_description;
    }

    public function getMetaDescription()
    {
        return $this->meta_description;
    }

    public function setMetaKeywords($meta_keywords)
    {
        $this->meta_keywords = $meta_keywords;
    }

    public function getMetaKeywords()
    {
        return $this->meta_keywords;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
        return $this;
    }
    public function getSlug()
    {
        return $this->slug;
    }

}