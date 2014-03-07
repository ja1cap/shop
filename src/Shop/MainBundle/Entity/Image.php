<?php

namespace Shop\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Image
 * @package Shop\MainBundle\Entity
 */
abstract class Image extends AbstractEntity
{
    /**
     * @var integer
     */
    private $id;

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
     * Set imageFileName
     *
     * @param string $imageFileName
     * @return Image
     */
    public function setImageFileName($imageFileName)
    {
        $this->imageFileName = $imageFileName;

        return $this;
    }

    /**
     * @var string
     */
    private $thumbImageFileName;


    /**
     * Set thumbImageFileName
     *
     * @param string $thumbImageFileName
     * @return Image
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

    public function getThumbImage(){
        return $this->getFile('thumbImageFileName');
    }

    public function setThumbImage($file = null){
        return $this->setFile('thumbImageFileName', $file);
    }

    public function getUrl(){
        return $this->getFileUrl($this->getImageFileName());
    }

    public function getThumbUrl(){
        return $this->getFileUrl($this->getThumbImageFileName()) ?: $this->getUrl();
    }

}
