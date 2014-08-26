<?php

namespace Shop\DiscountBundle\Entity;

use Shop\DiscountBundle\Element\ActionConditionElement;
use Weasty\Doctrine\Cache\Collection\CacheCollectionEntityInterface;
use Weasty\Money\Price\Price;
use Weasty\Doctrine\Entity\AbstractEntity;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class ActionCondition
 * @package Shop\DiscountBundle\Entity
 */
class ActionCondition extends AbstractEntity
    implements  ActionConditionInterface,
                CacheCollectionEntityInterface
{

    /**
     * @var integer
     */
    private $actionId;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Shop\DiscountBundle\Entity\Action
     */
    private $action;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $categories;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $proposals;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $gifts;

    /**
     * @var integer
     */
    private $type;

    /**
     * @var integer
     */
    private $discountType;

    /**
     * @var float
     */
    private $discountPercent;

    /**
     * @var float
     */
    private $discountPriceValue;

    /**
     * @var integer
     */
    private $discountPriceCurrencyNumericCode;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $discountCategories;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $discountProposals;

    /**
     * @var integer
     */
    private $priority;

    /**
     * @var boolean
     */
    private $isComplex;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->priority = 1;
        $this->gifts = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->discountCategories = new ArrayCollection();
        $this->proposals = new ArrayCollection();
        $this->discountProposals = new ArrayCollection();
    }

    /**
     * @return string
     */
    function __toString()
    {
        return (string)$this->getId();
    }

    /**
     * @param $collection \Weasty\Doctrine\Cache\Collection\CacheCollection
     * @return \Weasty\Doctrine\Cache\Collection\CacheCollectionElementInterface
     */
    public function createCollectionElement($collection)
    {
        return new ActionConditionElement($collection, $this);
    }

    /**
     * @return boolean
     */
    public function getIsPriceDiscount()
    {
        return in_array($this->getDiscountType(), [
            self::DISCOUNT_TYPE_PRICE,
            self::DISCOUNT_TYPE_GIFT_AND_PRICE,
            self::DISCOUNT_TYPE_GIFT_OR_PRICE,
            self::DISCOUNT_TYPE_PERCENT,
            self::DISCOUNT_TYPE_GIFT_AND_PERCENT,
            self::DISCOUNT_TYPE_GIFT_OR_PERCENT,
        ]);
    }

    /**
     * Set actionId
     *
     * @param integer $actionId
     * @return ActionCondition
     */
    public function setActionId($actionId)
    {
        $this->actionId = $actionId;

        return $this;
    }

    /**
     * Get actionId
     *
     * @return integer 
     */
    public function getActionId()
    {
        return $this->actionId;
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
     * Set action
     *
     * @param \Shop\DiscountBundle\Entity\ActionInterface $action
     * @return ActionCondition
     */
    public function setAction(ActionInterface $action = null)
    {
        $this->action = $action;
        $this->actionId = $action->getId();
        return $this;
    }

    /**
     * Get action
     *
     * @return \Shop\DiscountBundle\Entity\Action
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return ActionCondition
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set priority
     *
     * @param integer $priority
     * @return ActionCondition
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return integer 
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set isComplex
     *
     * @param boolean $flag
     * @return ActionCondition
     */
    public function setIsComplex($flag)
    {
        $this->isComplex = $flag;

        return $this;
    }

    /**
     * Get isComplex
     *
     * @return boolean 
     */
    public function getIsComplex()
    {
        return $this->isComplex;
    }

    /**
     * Set discountType
     *
     * @param integer $discountType
     * @return ActionCondition
     */
    public function setDiscountType($discountType)
    {
        $this->discountType = $discountType;

        return $this;
    }

    /**
     * Get discountType
     *
     * @return integer
     */
    public function getDiscountType()
    {
        return $this->discountType;
    }

    /**
     * Set discountPriceValue
     *
     * @param float $discountValue
     * @return ActionCondition
     */
    public function setDiscountPriceValue($discountValue)
    {
        $this->discountPriceValue = $discountValue;

        return $this;
    }

    /**
     * Get discountPriceValue
     *
     * @return float
     */
    public function getDiscountPriceValue()
    {
        return $this->discountPriceValue;
    }

    /**
     * Set discountPriceCurrencyNumericCode
     *
     * @param integer $discountCurrencyNumericCode
     * @return ActionCondition
     */
    public function setDiscountPriceCurrencyNumericCode($discountCurrencyNumericCode)
    {
        $this->discountPriceCurrencyNumericCode = $discountCurrencyNumericCode;

        return $this;
    }

    /**
     * Get discountPriceCurrencyNumericCode
     *
     * @return integer
     */
    public function getDiscountPriceCurrencyNumericCode()
    {
        return $this->discountPriceCurrencyNumericCode;
    }

    /**
     * @return null|\Weasty\Money\Price\PriceInterface
     */
    public function getDiscountPrice(){
        return new Price($this->getDiscountPriceValue(), $this->getDiscountPriceCurrencyNumericCode());
    }

    /**
     * @param null|array|\Weasty\Money\Price\PriceInterface $price
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function setDiscountPrice($price){

        $value = null;
        if($price){

            if(isset($price['value'])){
                $value = $price['value'];
            }

            if($price && isset($price['currency'])){
                $currency = $price['currency'];
                if(!is_numeric($currency)){
                    throw new \InvalidArgumentException('Currency must have numeric type');
                }
                $this->setDiscountPriceCurrencyNumericCode($currency);
            }

        }

        $this->setDiscountPriceValue($value);

        return $this;
    }

    /**
     * Set discountPercent
     *
     * @param float $discountPercent
     * @return ActionCondition
     */
    public function setDiscountPercent($discountPercent)
    {
        $this->discountPercent = $discountPercent;

        return $this;
    }

    /**
     * Get discountPercent
     *
     * @return float 
     */
    public function getDiscountPercent()
    {
        return $this->discountPercent;
    }

    /**
     * Add categories
     *
     * @param \Shop\DiscountBundle\Entity\ActionConditionCategory $categories
     * @return ActionCondition
     */
    public function addCategory(ActionConditionCategory $categories)
    {
        $this->categories[] = $categories;

        return $this;
    }

    /**
     * Remove categories
     *
     * @param \Shop\DiscountBundle\Entity\ActionConditionCategory $categories
     */
    public function removeCategory(ActionConditionCategory $categories)
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
    public function getCategoryIds(){

        return $this->getCategories()->map(function(ActionConditionCategory $actionConditionCategory){
            return $actionConditionCategory->getCategoryId();
        })->toArray();

    }

    /**
     * Add proposals
     *
     * @param \Shop\DiscountBundle\Entity\ActionConditionProposal $proposals
     * @return ActionCondition
     */
    public function addProposal(ActionConditionProposal $proposals)
    {
        $this->proposals[] = $proposals;
        $proposals->setCondition($this);
        return $this;
    }

    /**
     * Remove proposals
     *
     * @param \Shop\DiscountBundle\Entity\ActionConditionProposal $proposals
     */
    public function removeProposal(ActionConditionProposal $proposals)
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
     * @return array
     */
    public function getProposalIds(){
        return $this->getProposals()->map(function(ActionConditionProposal $actionConditionProposal){
            return $actionConditionProposal->getProposalId();
        })->toArray();
    }

    /**
     * Add discountCategories
     *
     * @param \Shop\DiscountBundle\Entity\ActionConditionDiscountCategory $discountCategories
     * @return ActionCondition
     */
    public function addDiscountCategory(ActionConditionDiscountCategory $discountCategories)
    {
        $this->discountCategories[] = $discountCategories;

        return $this;
    }

    /**
     * Remove discountCategories
     *
     * @param \Shop\DiscountBundle\Entity\ActionConditionDiscountCategory $discountCategories
     */
    public function removeDiscountCategory(ActionConditionDiscountCategory $discountCategories)
    {
        $this->discountCategories->removeElement($discountCategories);
    }

    /**
     * Get discountCategories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDiscountCategories()
    {
        return $this->discountCategories;
    }

    /**
     * @return array
     */
    public function getDiscountCategoryIds(){
        return $this->getDiscountCategories()->map(function(ActionConditionDiscountCategory $actionConditionDiscountCategory){
            return $actionConditionDiscountCategory->getCategoryId();
        })->toArray();
    }

    /**
     * Add discountProposals
     *
     * @param \Shop\DiscountBundle\Entity\ActionConditionDiscountProposal $discountProposal
     * @return ActionCondition
     */
    public function addDiscountProposal(ActionConditionDiscountProposal $discountProposal)
    {
        $this->discountProposals[] = $discountProposal;
        $discountProposal->setCondition($this);
        return $this;
    }

    /**
     * Remove discountProposals
     *
     * @param \Shop\DiscountBundle\Entity\ActionConditionDiscountProposal $discountProposal
     */
    public function removeDiscountProposal(ActionConditionDiscountProposal $discountProposal)
    {
        $this->discountProposals->removeElement($discountProposal);
    }

    /**
     * Get discountProposals
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDiscountProposals()
    {
        return $this->discountProposals;
    }

    /**
     * @return array
     */
    public function getDiscountProposalIds(){
        return $this->getDiscountProposals()->map(function(ActionConditionDiscountProposal $actionConditionDiscountProposal){
            return $actionConditionDiscountProposal->getProposalId();
        })->toArray();
    }


    /**
     * Add gifts
     *
     * @param \Shop\DiscountBundle\Entity\ActionConditionGiftProposal $gift
     * @return ActionCondition
     */
    public function addGift(ActionConditionGiftProposal $gift)
    {
        $this->gifts[] = $gift;
        $gift->setCondition($this);
        return $this;
    }

    /**
     * Remove gifts
     *
     * @param \Shop\DiscountBundle\Entity\ActionConditionGiftProposal $gift
     */
    public function removeGift(ActionConditionGiftProposal $gift)
    {
        $this->gifts->removeElement($gift);
    }

    /**
     * Get gifts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGifts()
    {
        return $this->gifts;
    }

    /**
     * @return array
     */
    public function getGiftProposalIds(){
        return $this->gifts->map(function(ActionConditionGiftProposal $giftProposal){
            return $giftProposal->getProposalId();
        })->toArray();
    }

}
