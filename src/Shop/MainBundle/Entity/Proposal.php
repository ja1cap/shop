<?php

namespace Shop\MainBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Proposal
 */
abstract class Proposal extends AbstractEntity
{

    const ROUTE = null;
    const TYPE = null;

    const PROPOSAL_TYPE_SINGLE_NAME = 'Предложение';
    const PROPOSAL_TYPE_MULTIPLE_NAME = 'Предложения';

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
     * @return \Shop\MainBundle\Entity\AbstractPrice
     */
    abstract public function createPrice();

    /**
     * Add prices
     *
     * @param \Shop\MainBundle\Entity\AbstractPrice $price
     * @return Proposal
     */
    public function addPrice(AbstractPrice $price)
    {
        $this->prices[] = $price;
        $price->setProposal($this);
        return $this;
    }

    /**
     * Remove prices
     *
     * @param \Shop\MainBundle\Entity\AbstractPrice $price
     */
    public function removePrice(AbstractPrice $price)
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
        $price = $this->getPrices()->current();
        if($price instanceof AbstractPrice){
            return $price->getValue();
        }
        return null;
    }

    /**
     * @return \Symfony\Component\Form\AbstractType
     */
    abstract public function getForm();

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
     * @return string
     */
    abstract public function getRoute();

}
