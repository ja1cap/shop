<?php

namespace Shop\CatalogBundle\Entity;

use Application\Sonata\MediaBundle\Entity\Media;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Shop\CatalogBundle\Proposal\ProposalElement;
use Shop\CatalogBundle\Proposal\ProposalInterface;
use Weasty\Doctrine\Cache\Collection\CacheCollectionEntityInterface;
use Weasty\Doctrine\Entity\AbstractEntity;

/**
 * Class Proposal
 * @package Shop\CatalogBundle\Entity
 */
class Proposal extends AbstractEntity
    implements  ProposalInterface,
                CacheCollectionEntityInterface
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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $prices;

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
     * @var string
     */
    private $seoSlug;

    /**
     * @var integer
     */
    private $categoryId;

    /**
     * @var \Shop\CatalogBundle\Entity\Category
     */
    private $category;

    /**
     * @var integer
     */
    private $manufacturerId;

    /**
     * @var \Shop\CatalogBundle\Entity\Manufacturer
     */
    private $manufacturer;

    /**
     * @var integer
     */
    private $defaultContractorId;

    /**
     * @var \Shop\CatalogBundle\Entity\Contractor
     */
    private $defaultContractor;

    /**
     * @var integer
     */
    private $status;

    /**
     * @var integer
     */
    private $mainMediaImageId;

    /**
     * @var \Application\Sonata\MediaBundle\Entity\Media
     */
    private $mainMediaImage;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $mediaImages;

    /**
     * @var boolean
     */
    private $isNew;

    /**
     * @var boolean
     */
    private $isBestseller;

    /**
     * @var \DateTime
     */
    private $createDate;

    /**
     * @var \DateTime
     */
    private $updateDate;

    /**
     * @var array
     */
    public static $statuses = array(
        self::STATUS_ON => 'Вкл',
        self::STATUS_OFF => 'Выкл',
    );

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->prices = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->mediaImages = new ArrayCollection();
    }

    /**
     * @param $collection \Weasty\Doctrine\Cache\Collection\CacheCollection
     * @return \Weasty\Doctrine\Cache\Collection\CacheCollectionElementInterface
     */
    public function createCollectionElement($collection)
    {
        return new ProposalElement($collection, $this);
    }

    /**
     * @return array
     */
    public function getRouteParameters()
    {
        return [
            'categorySlug' => $this->getCategory()->getSlug(),
            'slug' => $this->getSlug(),
        ];
    }

    /**
     * @return string
     */
    function __toString()
    {
        return $this->getName();
    }

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
     * @return string
     */
    public function getName()
    {
        return $this->getTitle();
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return ($this->getSeoSlug() ?: $this->getId());
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

        if(mb_strlen($this->shortDescription, 'UTF-8') > 100){
            return mb_substr($this->shortDescription, 'UTF-8', 0, 97) . '...';
        }

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

        return $price;

    }

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
     * @return null|string
     */
    public function getCategoryName(){
        return $this->getCategory() ? $this->getCategory()->getName() : null;
    }

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
        $this->manufacturerId = $manufacturer ? $manufacturer->getId() : null;
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
     * @return null|string
     */
    public function getManufacturerName(){
        return $this->getManufacturer() ? $this->getManufacturer()->getName() : null;
    }

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

    /**
     * Add mediaImages
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media $mediaImages
     * @return Proposal
     */
    public function addMediaImage(Media $mediaImages)
    {
        $this->mediaImages[] = $mediaImages;

        return $this;
    }

    /**
     * Remove mediaImages
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media $mediaImages
     */
    public function removeMediaImage(Media $mediaImages)
    {
        $this->mediaImages->removeElement($mediaImages);
    }

    /**
     * Get mediaImages
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMediaImages()
    {
        return $this->mediaImages;
    }

    /**
     * Set mainMediaImageId
     *
     * @param integer $mainMediaImageId
     * @return Proposal
     */
    public function setMainMediaImageId($mainMediaImageId)
    {
        $this->mainMediaImageId = $mainMediaImageId;

        return $this;
    }

    /**
     * Get mainMediaImageId
     *
     * @return integer 
     */
    public function getMainMediaImageId()
    {
        return $this->mainMediaImageId;
    }

    /**
     * Set mainMediaImage
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media $mainMediaImage
     * @return Proposal
     */
    public function setMainMediaImage(Media $mainMediaImage = null)
    {
        $this->mainMediaImage = $mainMediaImage;
        $this->mainMediaImageId = $mainMediaImage ? $mainMediaImage->getId() : null;
        return $this;
    }

    /**
     * Get mainMediaImage
     *
     * @return \Application\Sonata\MediaBundle\Entity\Media 
     */
    public function getMainMediaImage()
    {
        return $this->mainMediaImage;
    }

    /**
     * @return int
     */
    public function getImageId(){
        return $this->getMainMediaImageId();
    }

    /**
     * @return \Application\Sonata\MediaBundle\Entity\Media
     */
    public function getImage(){
        return $this->getMainMediaImage();
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImages(){
        return $this->getMediaImages();
    }

    /**
     * @return int[]
     */
    public function getImageIds(){

        $ids = [];

        /**
         * @var \Sonata\MediaBundle\Model\MediaInterface $image
         */
        foreach($this->getImages() as $image){
            $ids[] = $image->getId();
        }

        return $ids;

    }

    /**
     * Set createDate
     *
     * @param \DateTime $createDate
     * @return Proposal
     */
    public function setCreateDate($createDate)
    {
        $this->createDate = $createDate;

        return $this;
    }

    /**
     * Get createDate
     *
     * @return \DateTime 
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }

    /**
     * Set updateDate
     *
     * @param \DateTime $updateDate
     * @return Proposal
     */
    public function setUpdateDate($updateDate)
    {
        $this->updateDate = $updateDate;

        return $this;
    }

    /**
     * Get updateDate
     *
     * @return \DateTime 
     */
    public function getUpdateDate()
    {
        return $this->updateDate;
    }

    public function updateDate(){

        if($this->createDate === null){
            $this->createDate = new \DateTime();
        }

        $this->updateDate = new \DateTime();

    }

    /**
     * Set isNew
     *
     * @param boolean $isNew
     * @return Proposal
     */
    public function setIsNew($isNew)
    {
        $this->isNew = $isNew;

        return $this;
    }

    /**
     * Get isNew
     *
     * @return boolean 
     */
    public function getIsNew()
    {
        return $this->isNew;
    }

    /**
     * Set isBestseller
     *
     * @param boolean $isBestseller
     * @return Proposal
     */
    public function setIsBestseller($isBestseller)
    {
        $this->isBestseller = $isBestseller;

        return $this;
    }

    /**
     * Get isBestseller
     *
     * @return boolean 
     */
    public function getIsBestseller()
    {
        return $this->isBestseller;
    }
}
