<?php
namespace  G\ContentBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Gedmo\Tree(type="nested")
 * @ORM\Table(name="content")
 * @ORM\Entity
 * @Gedmo\Loggable
 * @ORM\Entity(repositoryClass="ContentRepository")
 */
class Content
{
    use \A2lix\TranslationFormBundle\Util\Gedmo\GedmoTranslatable;

    /** @ORM\Id @ORM\GeneratedValue @ORM\Column(type="integer") */
    protected $id;

    /**
     * @Gedmo\Versioned
     * @Gedmo\TreeRoot
     * @ORM\Column(name="root", type="integer", nullable=true)
     */
    protected $root;

    /**
     * @Gedmo\Versioned
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Content", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $parent;

    /**
     * @Gedmo\Versioned
     * @Gedmo\TreeLeft
     * @ORM\Column(name="lft", type="integer")
     */
    protected $lft;

    /**
     * @Gedmo\Versioned
     * @Gedmo\TreeLevel
     * @ORM\Column(name="lvl", type="integer")
     */
    protected $lvl;

    /**
     * @Gedmo\Versioned
     * @Gedmo\TreeRight
     * @ORM\Column(name="rgt", type="integer")
     */
    protected $rgt;

    /**
    * Slug should be nullable, since it is generated on
    * translation entity, this fields serves only for
    * representation
    *
    * @Gedmo\Translatable
    * @ORM\Column(length=255, nullable=true)
    */
    protected $slug;

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
     * @Gedmo\Translatable
     * @ORM\Column(name="meta_title", type="string", length=255, nullable=true)
     */
    protected $meta_title;

    /**
     * @Gedmo\Versioned
     * @Gedmo\Translatable
     * @ORM\Column(name="meta_description", type="text", nullable=true)
     */
    protected $meta_description;

    /**
     * @Gedmo\Versioned
     * @Gedmo\Translatable
     * @ORM\Column(name="meta_keywords", type="text", nullable=true)
     */
    protected $meta_keywords;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(name="rank", type="integer", options={"default" = 0}, nullable=true)
     */
    protected $rank;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(name="is_hidden", type="boolean", options={"default" = 0}, nullable=true)
     */
    protected $is_hidden;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(name="is_visible", type="boolean", options={"default" = 0}, nullable=true)
     */
    protected $is_visible;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(name="is_system", type="boolean", options={"default" = 0}, nullable=true)
     */
    protected $is_system;

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
     * @ORM\OneToMany(targetEntity="Content", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    protected $children;

    /** 
     * @ORM\OneToMany(targetEntity="G\ContentBundle\Entity\ContentTranslation", mappedBy="object", cascade={"persist", "remove"}, indexBy="locale")
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

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($value)
    {
        $this->description = $value;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setParent(Content $parent = null)
    {
        $this->parent = $parent;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function getIsHidden()
    {
        return $this->is_hidden;
    }

    public function setIsHidden($is_hidden)
    {
        $this->is_hidden = $is_hidden;
    }

    public function getIsSystem()
    {
        return $this->is_system;
    }

    public function setIsSystem($is_system)
    {
        $this->is_system = $is_system;
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
     * Gets the value of lft.
     *
     * @return mixed
     */
    public function getLft()
    {
        return $this->lft;
    }
    
    /**
     * Sets the value of lft.
     *
     * @param mixed $lft the lft 
     */
    public function setLft($lft)
    {
        $this->lft = $lft;
    }

    /**
     * Gets the value of lvl.
     *
     * @return mixed
     */
    public function getLvl()
    {
        return $this->lvl;
    }
    
    /**
     * Sets the value of lvl.
     *
     * @param mixed $lvl the lvl 
     */
    public function setLvl($lvl)
    {
        $this->lvl = $lvl;
    }

    /**
     * Gets the value of rgt.
     *
     * @return mixed
     */
    public function getRgt()
    {
        return $this->rgt;
    }
    
    /**
     * Sets the value of rgt.
     *
     * @param mixed $rgt the rgt 
     */
    public function setRgt($rgt)
    {
        $this->rgt = $rgt;
    }

    /**
     * Gets the value of root.
     *
     * @return mixed
     */
    public function getRoot()
    {
        return $this->root;
    }
    
    /**
     * Sets the value of root.
     *
     * @param mixed $root the root 
     */
    public function setRoot($root)
    {
        $this->root = $root;
    }

    /**
     * Gets the value of children.
     *
     * @return mixed
     */
    public function getChildren()
    {
        return $this->children;
    }
    
    /**
     * Sets the value of children.
     *
     * @param mixed $children the children 
     */
    public function setChildren($children)
    {
        $this->children = $children;
    }

    public function __toString()
    {
        return $this->getTitle() ?: 'n/a';
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

    /**
     * Gets the value of is_visible.
     *
     * @return mixed
     */
    public function getIsVisible()
    {
        return $this->is_visible;
    }

    /**
     * Sets the value of is_visible.
     * @param $value boolean
     * @return null
     */
    public function setIsVisible($value)
    {
        $this->is_visible = $value;
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
}