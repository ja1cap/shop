<?php

namespace Shop\CatalogBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Weasty\DoctrineBundle\Entity\AbstractEntity;

/**
 * Class Action
 * @package Shop\MainBundle\Entity]
 */
class Action extends AbstractEntity
{

    const STATUS_ON = 1;
    const STATUS_OFF = 2;

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
     * @var array
     */
    public static $statuses = array(
        self::STATUS_ON => 'Вкл',
        self::STATUS_OFF => 'Выкл',
    );

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

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }

    /**
     * @var integer
     */
    private $minOrderSummary;

    /**
     * @var integer
     */
    private $maxOrderSummary;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $categories;


    /**
     * Set minOrderSummary
     *
     * @param integer $minOrderSummary
     * @return Action
     */
    public function setMinOrderSummary($minOrderSummary)
    {
        $this->minOrderSummary = $minOrderSummary;

        return $this;
    }

    /**
     * Get minOrderSummary
     *
     * @return integer 
     */
    public function getMinOrderSummary()
    {
        return $this->minOrderSummary;
    }

    /**
     * Set maxOrderSummary
     *
     * @param integer $maxOrderSummary
     * @return Action
     */
    public function setMaxOrderSummary($maxOrderSummary)
    {
        $this->maxOrderSummary = $maxOrderSummary;

        return $this;
    }

    /**
     * Get maxOrderSummary
     *
     * @return integer 
     */
    public function getMaxOrderSummary()
    {
        return $this->maxOrderSummary;
    }

    /**
     * Add categories
     *
     * @param \Shop\CatalogBundle\Entity\Category $categories
     * @return Action
     */
    public function addCategory(Category $categories)
    {
        $this->categories[] = $categories;

        return $this;
    }

    /**
     * Remove categories
     *
     * @param \Shop\CatalogBundle\Entity\Category $categories
     */
    public function removeCategory(Category $categories)
    {
        $this->categories->removeElement($categories);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @return array
     */
    public function getCategoriesNames(){
        return $this->getCategories()->map(function(Category $category){
            return $category->getName();
        })->toArray();
    }
    /**
     * @var integer
     */
    private $position;


    /**
     * Set position
     *
     * @param integer $position
     * @return Action
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer 
     */
    public function getPosition()
    {
        return $this->position;
    }
    /**
     * @var integer
     */
    private $status;


    /**
     * Set status
     *
     * @param integer $status
     * @return Action
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }
}
