<?php

namespace Shop\DiscountBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Shop\DiscountBundle\Action\ActionInterface;
use Shop\DiscountBundle\ActionCondition\ActionConditionInterface;
use Shop\DiscountBundle\Category\ActionCategoryInterface;
use Shop\DiscountBundle\Proposal\ActionProposalInterface;
use Weasty\Doctrine\Cache\Collection\CacheCollectionElement;
use Weasty\Doctrine\Cache\Collection\CacheCollectionEntityInterface;
use Weasty\Doctrine\Entity\AbstractEntity;
use Application\Sonata\MediaBundle\Entity\Media;

/**
 * Class Action
 * @package Shop\DiscountBundle\Entity
 */
class Action extends AbstractEntity
    implements  ActionInterface,
                CacheCollectionEntityInterface
{

    /**
     * @var integer
     */
    private $id;

    /**
     * @var array
     */
    public static $statuses = array(
        self::STATUS_ON => 'Вкл',
        self::STATUS_OFF => 'Выкл',
    );

    /**
     * @var integer
     */
    private $status;

    /**
     * @var integer
     */
    private $position;

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
    private $content;

    /**
     * @var integer
     */
    private $basicConditionId;

    /**
     * @var \Shop\DiscountBundle\Entity\BasicActionCondition
     */
    private $basicCondition;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $conditions;

    /**
     * @var integer
     */
    private $imageId;

    /**
     * @var \Application\Sonata\MediaBundle\Entity\Media
     */
    private $image;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->conditions = new ArrayCollection();
    }

    /**
     * @param $collection \Weasty\Doctrine\Cache\Collection\CacheCollection
     * @return \Weasty\Doctrine\Cache\Collection\CacheCollectionElementInterface
     */
    public function createCollectionElement($collection)
    {
        return new CacheCollectionElement($collection, $this);
    }

    /**
     * @return string
     */
    function __toString()
    {
        return $this->getTitle();
    }

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

    /**
     * @return mixed
     */
    public function getTextStatus(){
        return self::$statuses[$this->getStatus()];
    }

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
     * @return string
     */
    public function getName(){
        return $this->getTitle();
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
     * Set content
     *
     * @param string $content
     * @return Action
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
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
     * Add conditions
     *
     * @param \Shop\DiscountBundle\ActionCondition\ActionConditionInterface $condition
     * @return Action
     */
    public function addCondition(ActionConditionInterface $condition)
    {
        $this->conditions[] = $condition;
        $condition->setAction($this);
        return $this;
    }

    /**
     * Remove conditions
     *
     * @param \Shop\DiscountBundle\Entity\ActionCondition $conditions
     */
    public function removeCondition(ActionCondition $conditions)
    {
        $this->conditions->removeElement($conditions);
    }

    /**
     * Get conditions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * @return \Shop\DiscountBundle\Category\ActionCategoryInterface[]
     */
    public function getActionCategories()
    {

        $actionCategories = [];

        foreach($this->getConditions() as $condition){

            if($condition instanceof ActionCategoryInterface){
                $actionCategories[] = $condition;
            }

        }

        return $actionCategories;

    }

    /**
     * Get \Shop\CatalogBundle\Entity\Category ids
     * @return array
     */
    public function getCategoryIds()
    {
        $ids = [];
        foreach($this->getActionCategories() as $actionCategory){
            $ids[] = $actionCategory->getCategoryId();
        }
        return $ids;
    }

    /**
     * @return \Shop\DiscountBundle\Proposal\ActionProposalInterface[]
     */
    public function getActionProposals()
    {

        $actionProposals = [];

        foreach($this->getConditions() as $condition){

            if($condition instanceof ActionProposalInterface){
                $actionProposals[] = $condition;
            }

        }

        return $actionProposals;

    }

    /**
     * Get \Shop\CatalogBundle\Entity\Proposal ids
     * @return array
     */
    public function getProposalIds()
    {
        $ids = [];
        foreach($this->getActionProposals() as $actionProposal){
            $ids[] = $actionProposal->getProposalId();
        }
        return $ids;
    }

    /**
     * Set imageId
     *
     * @param integer $imageId
     * @return Action
     */
    public function setImageId($imageId)
    {
        $this->imageId = $imageId;

        return $this;
    }

    /**
     * Get imageId
     *
     * @return integer 
     */
    public function getImageId()
    {
        return $this->imageId;
    }

    /**
     * Set image
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media $image
     * @return Action
     */
    public function setImage(Media $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \Application\Sonata\MediaBundle\Entity\Media 
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set basicConditionId
     *
     * @param integer $basicConditionId
     * @return Action
     */
    public function setBasicConditionId($basicConditionId)
    {
        $this->basicConditionId = $basicConditionId;

        return $this;
    }

    /**
     * Get basicConditionId
     *
     * @return integer 
     */
    public function getBasicConditionId()
    {
        return $this->basicConditionId;
    }

    /**
     * Set basicCondition
     *
     * @param \Shop\DiscountBundle\Entity\BasicActionCondition $basicCondition
     * @return Action
     */
    public function setBasicCondition(BasicActionCondition $basicCondition = null)
    {
        $this->basicCondition = $basicCondition;
        if($basicCondition){
            $basicCondition->setAction($this);
            $this->basicConditionId = $basicCondition->getId();
        } else {
            $this->basicConditionId = null;
        }
        return $this;
    }

    /**
     * Get basicCondition
     *
     * @return \Shop\DiscountBundle\Entity\BasicActionCondition 
     */
    public function getBasicCondition()
    {
        return $this->basicCondition;
    }
}
