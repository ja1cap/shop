<?php

namespace Shop\CatalogBundle\Entity;

use Application\Sonata\MediaBundle\Entity\Media;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use php_rutils\RUtils;
use Shop\CatalogBundle\CollectionElement\CategoryElement;
use Weasty\Doctrine\Cache\Collection\CacheCollectionEntityInterface;
use Weasty\Doctrine\Entity\AbstractEntity;
use Weasty\Bundle\CatalogBundle\Data\CategoryInterface;
use Weasty\Resource\Utils\WordInflector;

/**
 * Class Category
 * @package Shop\CatalogBundle\Entity
 */
class Category extends AbstractEntity
    implements CategoryInterface, CacheCollectionEntityInterface
{

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $slug;

    /**
     * @var string
     */
    private $singularName;

    /**
     * @var string
     */
    private $singularGenitiveName;

    /**
     * @var string
     */
    private $singularAblativusName;

    /**
     * @var string
     */
    private $singularAccusativusName;

    /**
     * @var string
     */
    private $multipleName;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $parameterGroups;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $parameters;

    /**
     * @var integer|null
     */
    private $imageId;

    /**
     * @var Media|null
     */
    private $image;

    /**
     * @var \DateTime
     */
    private $createDate;

    /**
     * @var \DateTime
     */
    private $updateDate;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->filters = new ArrayCollection();
        $this->parameters = new ArrayCollection();
        $this->parameterGroups = new ArrayCollection();
        $this->proposals = new ArrayCollection();
        $this->additionalCategories = new ArrayCollection();
    }
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
     * @param $collection \Weasty\Doctrine\Cache\Collection\CacheCollection
     * @return \Weasty\Doctrine\Cache\Collection\CacheCollectionElementInterface
     */
    public function createCollectionElement($collection)
    {
        return new CategoryElement($collection, $this);
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Category
     */
    public function setName($name)
    {
        $this->name = $name;
        $this->setSlug(RUtils::translit()->slugify($name));
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set createDate
     *
     * @param \DateTime $createDate
     * @return Category
     */
    public function setCreateDate($createDate)
    {
        $this->createDate = $createDate ?: new \DateTime();

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
     * @return Category
     */
    public function setUpdateDate($updateDate)
    {

        $this->updateDate = $updateDate ?: new \DateTime();

        return $this;
    }

    public function updateDate(){

        if($this->createDate === null){
            $this->createDate = new \DateTime();
        }

        $this->updateDate = new \DateTime();

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

    /**
     * Set slug
     *
     * @param string $slug
     * @return Category
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set singularName
     *
     * @param string $singularName
     * @return Category
     */
    public function setSingularName($singularName)
    {
        $singularName = strtolower($singularName);
        $this->singularName = $singularName;
        return $this;
    }

    /**
     * Get singularName
     *
     * @return string 
     */
    public function getSingularName()
    {
        return $this->singularName ?: $this->getName();
    }

    /**
     * @param string $singularGenitiveName
     * @return $this
     */
    public function setSingularGenitiveName($singularGenitiveName)
    {
        $this->singularGenitiveName = $singularGenitiveName;
        return $this;
    }

    /**
     * @return string
     */
    public function getSingularGenitiveName()
    {
        return $this->singularGenitiveName ?: WordInflector::inflect($this->getSingularName(), WordInflector::CASE_GENITIVE);
    }

    /**
     * Set singularAccusativusName
     *
     * @param string $singularAccusativusName
     * @return $this
     */
    public function setSingularAccusativusName($singularAccusativusName)
    {
        $this->singularAccusativusName = $singularAccusativusName;
        return $this;
    }

    /**
     * @return string
     */
    public function getSingularAccusativusName()
    {
        return $this->singularAccusativusName ?: WordInflector::inflect($this->getSingularName(), WordInflector::CASE_ACCUSATIVUS);
    }

    /**
     * @return string
     */
    public function getSingularAblativusName()
    {
        return $this->singularAblativusName ?: WordInflector::inflect($this->getSingularName(), WordInflector::CASE_ABLATIVUS);
    }

    /**
     * Set singularAblativusName
     *
     * @param string $singularAblativusName
     * @return $this
     */
    public function setSingularAblativusName($singularAblativusName)
    {
        $this->singularAblativusName = $singularAblativusName;

        return $this;
    }

    public function inflectName()
    {

        $singularName = $this->getSingularName();

        if($singularName){

            $this->setSingularGenitiveName(WordInflector::inflect($singularName, WordInflector::CASE_GENITIVE));
            $this->setSingularAccusativusName(WordInflector::inflect($singularName, WordInflector::CASE_ACCUSATIVUS));
            $this->setSingularAblativusName(WordInflector::inflect($singularName, WordInflector::CASE_ABLATIVUS));

        }

    }

    /**
     * Set multipleName
     *
     * @param string $multipleName
     * @return Category
     */
    public function setMultipleName($multipleName)
    {
        $multipleName = strtolower($multipleName);
        $this->multipleName = $multipleName;
        return $this;
    }

    /**
     * Get multipleName
     *
     * @return string 
     */
    public function getMultipleName()
    {
        return $this->multipleName ?: $this->getName();
    }

    /**
     * Add parameters
     *
     * @param \Shop\CatalogBundle\Entity\CategoryParameter $parameters
     * @return Category
     */
    public function addParameter(CategoryParameter $parameters)
    {
        $this->parameters[] = $parameters;
        $parameters->setCategory($this);
        return $this;
    }

    /**
     * Remove parameters
     *
     * @param \Shop\CatalogBundle\Entity\CategoryParameter $parameters
     */
    public function removeParameter(CategoryParameter $parameters)
    {
        $this->parameters->removeElement($parameters);
    }

    /**
     * Get parameters
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $proposals;


    /**
     * Add proposals
     *
     * @param \Shop\CatalogBundle\Entity\Proposal $proposals
     * @return Category
     */
    public function addProposal(Proposal $proposals)
    {
        $this->proposals[] = $proposals;
        $proposals->setCategory($this);
        return $this;
    }

    /**
     * Remove proposals
     *
     * @param \Shop\CatalogBundle\Entity\Proposal $proposals
     */
    public function removeProposal(Proposal $proposals)
    {
        $this->proposals->removeElement($proposals);
    }

    /**
     * Get proposals
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProposals()
    {
        return $this->proposals;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $additionalCategories;


    /**
     * Add additionalCategories
     *
     * @param \Shop\CatalogBundle\Entity\Category $additionalCategories
     * @return Category
     */
    public function addAdditionalCategory(Category $additionalCategories)
    {
        $this->additionalCategories[] = $additionalCategories;

        return $this;
    }

    /**
     * Remove additionalCategories
     *
     * @param \Shop\CatalogBundle\Entity\Category $additionalCategories
     */
    public function removeAdditionalCategory(Category $additionalCategories)
    {
        $this->additionalCategories->removeElement($additionalCategories);
    }

    /**
     * Get additionalCategories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAdditionalCategories()
    {
        return $this->additionalCategories;
    }

    /**
     * @return string
     */
    function __toString()
    {
        return $this->getName();
    }

    /**
     * @var integer
     */
    private $status;


    /**
     * Set status
     *
     * @param integer $status
     * @return Category
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
     * @return mixed
     */
    public function getTextStatus(){
        return self::$statuses[$this->getStatus()];
    }

    /**
     * Add parameterGroups
     *
     * @param \Shop\CatalogBundle\Entity\CategoryParameterGroup $parameterGroup
     * @return Category
     */
    public function addParameterGroup(CategoryParameterGroup $parameterGroup)
    {
        $this->parameterGroups[] = $parameterGroup;
        $parameterGroup->setCategory($this);
        return $this;
    }

    /**
     * Remove parameterGroups
     *
     * @param \Shop\CatalogBundle\Entity\CategoryParameterGroup $parameterGroups
     */
    public function removeParameterGroup(CategoryParameterGroup $parameterGroups)
    {
        $this->parameterGroups->removeElement($parameterGroups);
    }

    /**
     * Get parameterGroups
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getParameterGroups()
    {
        return $this->parameterGroups;
    }

    /**
     * @return int|null
     */
    public function getImageId()
    {
        return $this->imageId;
    }

    /**
     * @param int|null $imageId
     * @return $this
     */
    public function setImageId($imageId)
    {
        $this->imageId = $imageId;
        return $this;
    }

    /**
     * Set image
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media $image
     * @return Category
     */
    public function setImage(Media $image = null)
    {
        $this->image = $image;
        return $this;
    }

    /**
     * Get media
     *
     * @return \Application\Sonata\MediaBundle\Entity\Media 
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $filters;

    /**
     * Add filters
     *
     * @param \Shop\CatalogBundle\Entity\CategoryFilters $filters
     * @return Category
     */
    public function addFilter(CategoryFilters $filters)
    {
        $this->filters[] = $filters;
        $filters->setCategory($this);
        return $this;
    }

    /**
     * Remove filters
     *
     * @param \Shop\CatalogBundle\Entity\CategoryFilters $filters
     */
    public function removeFilter(CategoryFilters $filters)
    {
        $this->filters->removeElement($filters);
    }

    /**
     * Get filters
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFilters()
    {
        return $this->filters;
    }

}
