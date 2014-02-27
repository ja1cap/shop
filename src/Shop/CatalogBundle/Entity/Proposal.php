<?php

namespace Shop\CatalogBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Shop\MainBundle\Entity\AbstractEntity;

/**
 * Class Proposal
 * @package Shop\CatalogBundle\Entity
 */
class Proposal extends AbstractEntity
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
    private $shortDescription;

    /**
     * @var string
     */
    private $description;

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
     * @return Proposal
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
     * Set shortDescription
     *
     * @param string $shortDescription
     * @return Proposal
     */
    public function setShortDescription($shortDescription)
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    /**
     * Get shortDescription
     *
     * @return string 
     */
    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Proposal
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
     * Set imageFileName
     *
     * @param string $imageFileName
     * @return Proposal
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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $prices;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->prices = new ArrayCollection();
    }

    /**
     * Add prices
     *
     * @param \Shop\CatalogBundle\Entity\Price $price
     * @return Proposal
     */
    public function addPrice(Price $price)
    {
        $this->prices[] = $price;
        $price->setProposal($this);
        return $this;
    }

    /**
     * Remove prices
     *
     * @param \Shop\CatalogBundle\Entity\Price $price
     */
    public function removePrice(Price $price)
    {
        $this->prices->removeElement($price);
    }

    /**
     * Get prices
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPrices()
    {
        return $this->prices;
    }

    /**
     * @return null|string
     */
    public function getPrice(){

        $price = $this->getPrices()->filter(function(Price $price){
            return $price->getStatus() == Price::STATUS_ON;
        })->current();

        if($price instanceof Price){
            return $price->getExchangedValue();
        }

        return null;
    }

    /**
     * @var string
     */
    private $seoTitle;

    /**
     * @var string
     */
    private $seoDescription;

    /**
     * @var string
     */
    private $seoKeywords;


    /**
     * Set seoTitle
     *
     * @param string $seoTitle
     * @return Proposal
     */
    public function setSeoTitle($seoTitle)
    {
        $this->seoTitle = $seoTitle;

        return $this;
    }

    /**
     * Get seoTitle
     *
     * @return string 
     */
    public function getSeoTitle()
    {
        return $this->seoTitle;
    }

    /**
     * Set seoDescription
     *
     * @param string $seoDescription
     * @return Proposal
     */
    public function setSeoDescription($seoDescription)
    {
        $this->seoDescription = $seoDescription;

        return $this;
    }

    /**
     * Get seoDescription
     *
     * @return string 
     */
    public function getSeoDescription()
    {
        return $this->seoDescription;
    }

    /**
     * Set seoKeywords
     *
     * @param string $seoKeywords
     * @return Proposal
     */
    public function setSeoKeywords($seoKeywords)
    {
        $this->seoKeywords = $seoKeywords;

        return $this;
    }

    /**
     * Get seoKeywords
     *
     * @return string 
     */
    public function getSeoKeywords()
    {
        return $this->seoKeywords;
    }
    /**
     * @var string
     */
    private $thumbImageFileName;


    /**
     * Set thumbImageFileName
     *
     * @param string $thumbImageFileName
     * @return Proposal
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
     * @var boolean
     */
    private $showOnHomePage;


    /**
     * Set showOnHomePage
     *
     * @param boolean $showOnHomePage
     * @return Proposal
     */
    public function setShowOnHomePage($showOnHomePage)
    {
        $this->showOnHomePage = $showOnHomePage;

        return $this;
    }

    /**
     * Get showOnHomePage
     *
     * @return boolean 
     */
    public function getShowOnHomePage()
    {
        return $this->showOnHomePage;
    }
    /**
     * @var string
     */
    private $seoSlug;


    /**
     * Set seoSlug
     *
     * @param string $seoSlug
     * @return Proposal
     */
    public function setSeoSlug($seoSlug)
    {
        $this->seoSlug = $seoSlug;

        return $this;
    }

    /**
     * Get seoSlug
     *
     * @return string 
     */
    public function getSeoSlug()
    {
        return $this->seoSlug;
    }

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $parameterValues;


    /**
     * Add parameterValues
     *
     * @param \Shop\CatalogBundle\Entity\ParameterValue $parameterValues
     * @return Proposal
     */
    public function addParameterValue(ParameterValue $parameterValues)
    {
        $this->parameterValues[] = $parameterValues;
        $parameterValues->setProposal($this);
        return $this;
    }

    /**
     * Remove parameterValues
     *
     * @param \Shop\CatalogBundle\Entity\ParameterValue $parameterValues
     */
    public function removeParameterValue(ParameterValue $parameterValues)
    {
        $this->parameterValues->removeElement($parameterValues);
    }

    /**
     * Get parameterValues
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getParameterValues()
    {
        return $this->parameterValues;
    }
    /**
     * @var integer
     */
    private $categoryId;

    /**
     * @var \Shop\CatalogBundle\Entity\Category
     */
    private $category;


    /**
     * Set categoryId
     *
     * @param integer $categoryId
     * @return Proposal
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    /**
     * Get categoryId
     *
     * @return integer 
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * Set category
     *
     * @param \Shop\CatalogBundle\Entity\Category $category
     * @return Proposal
     */
    public function setCategory(Category $category = null)
    {
        $this->category = $category;
        $this->setCategoryId($category->getId());
        return $this;
    }

    /**
     * Get category
     *
     * @return \Shop\CatalogBundle\Entity\Category 
     */
    public function getCategory()
    {
        return $this->category;
    }
    /**
     * @var integer
     */
    private $manufacturerId;

    /**
     * @var \Shop\CatalogBundle\Entity\Manufacturer
     */
    private $manufacturer;


    /**
     * Set manufacturerId
     *
     * @param integer $manufacturerId
     * @return Proposal
     */
    public function setManufacturerId($manufacturerId)
    {
        $this->manufacturerId = $manufacturerId;

        return $this;
    }

    /**
     * Get manufacturerId
     *
     * @return integer 
     */
    public function getManufacturerId()
    {
        return $this->manufacturerId;
    }

    /**
     * Set manufacturer
     *
     * @param \Shop\CatalogBundle\Entity\Manufacturer $manufacturer
     * @return Proposal
     */
    public function setManufacturer(Manufacturer $manufacturer = null)
    {
        $this->manufacturer = $manufacturer;
        $this->setManufacturerId($manufacturer->getId());
        return $this;
    }

    /**
     * Get manufacturer
     *
     * @return \Shop\CatalogBundle\Entity\Manufacturer 
     */
    public function getManufacturer()
    {
        return $this->manufacturer;
    }
    /**
     * @var integer
     */
    private $defaultContractorId;

    /**
     * @var \Shop\CatalogBundle\Entity\Contractor
     */
    private $defaultContractor;


    /**
     * Set defaultContractorId
     *
     * @param integer $defaultContractorId
     * @return Proposal
     */
    public function setDefaultContractorId($defaultContractorId)
    {
        $this->defaultContractorId = $defaultContractorId;

        return $this;
    }

    /**
     * Get defaultContractorId
     *
     * @return integer 
     */
    public function getDefaultContractorId()
    {
        return $this->defaultContractorId;
    }

    /**
     * Set defaultContractor
     *
     * @param \Shop\CatalogBundle\Entity\Contractor $defaultContractor
     * @return Proposal
     */
    public function setDefaultContractor(Contractor $defaultContractor = null)
    {
        $this->defaultContractor = $defaultContractor;
        $this->defaultContractorId = $defaultContractor ? $defaultContractor->getId() : null;
        return $this;
    }

    /**
     * Get defaultContractor
     *
     * @return \Shop\CatalogBundle\Entity\Contractor 
     */
    public function getDefaultContractor()
    {
        return $this->defaultContractor;
    }
    /**
     * @var integer
     */
    private $status;


    /**
     * Set status
     *
     * @param integer $status
     * @return Proposal
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