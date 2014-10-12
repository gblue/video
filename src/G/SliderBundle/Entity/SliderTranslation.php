<?php 

namespace G\SliderBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractTranslation;
use Symfony\Component\HttpFoundation\File\File;

/**
 * G\SliderBundle\Entity\SliderTranslation.php
 *
 * @ORM\Entity
 * @ORM\Table(name="slider_i18n", uniqueConstraints={@ORM\UniqueConstraint(name="lookup_unique_idx", columns={
 *     "locale", "object_id"
 *   })}
 * )
 * @Gedmo\Loggable
 */
class SliderTranslation extends AbstractTranslation
{

    /**
     * @ORM\ManyToOne(targetEntity="G\SliderBundle\Entity\Slider", inversedBy="translations")
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
    protected $description;

    /** 
     * @Gedmo\Versioned
     * @ORM\OneToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"})
     */
    protected $file;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(length=255, nullable=true)
     */
    private $alt;

    /**
     * Convinient constructor
     *
     * @param string $locale
     * @param string $title
     * @param string $description
     */
    public function __construct($locale = null, $title = null, $image = null, $alt = null)
    {
        $this->locale = $locale;
        $this->title = $title;
        $this->image = $image;
        $this->alt = $alt;
    }

    public function setImage($image)
    {
        if($image !== null) {
            $image->move($this->getUploadRootDir(), $image->getClientOriginalName());
            $this->image = $image->getClientOriginalName();
        }
    }

    public function getImage()
    {
        return new File($this->getUploadRootDir() . '/'. $this->image);
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

    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setAlt($alt)
    {
        $this->alt = $alt;
    }

    public function getAlt()
    {
        return $this->alt;
    }

    public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir().'/'.$this->path;
    }

    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->getUploadDir().'/'.$this->path;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/slider';
    }



    /**
     * Sets the value of file.
     *
     * @param mixed $file the file 
     *
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * Gets the value of file.
     *
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }
}