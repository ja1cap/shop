<?php

namespace Shop\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Action
 * @package Shop\MainBundle\Entity]
 */
class Action extends AbstractEntity
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $thumbImageFileName;

    /**
     * @var string
     */
    private $imageFileName;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Action
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Action
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set thumbImageFileName
     *
     * @param string $thumbImageFileName
     * @return Action
     */
    public function setThumbImageFileName($thumbImageFileName)
    {
        $this->thumbImageFileName = $thumbImageFileName;

        return $this;
    }

    /**
     * Get thumbImageFileName
     *
     * @return string 
     */
    public function getThumbImageFileName()
    {
        return $this->thumbImageFileName;
    }

    public function getThumbImage(){
        return $this->getFile('thumbImageFileName');
    }

    public function setThumbImage($file = null){
        return $this->setFile('thumbImageFileName', $file);
    }

    public function getThumbImageUrl(){
        return $this->getFileUrl($this->getThumbImageFileName()) ?: $this->getImageUrl();
    }

    /**
     * Set imageFileName
     *
     * @param string $imageFileName
     * @return Action
     */
    public function setImageFileName($imageFileName)
    {
        $this->imageFileName = $imageFileName;

        return $this;
    }

    /**
     * Get imageFileName
     *
     * @return string 
     */
    public function getImageFileName()
    {
        return $this->imageFileName;
    }

    public function getImage(){
        return $this->getFile('imageFileName');
    }

    public function setImage($file = null){
        return $this->setFile('imageFileName', $file);
    }

    public function getImageUrl(){
        return $this->getFileUrl($this->getImageFileName());
    }

}
