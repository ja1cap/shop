<?php

namespace Shop\DiscountBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Shop\CatalogBundle\Entity\Category;
use Shop\CatalogBundle\Entity\Proposal;
use Weasty\Doctrine\Entity\AbstractEntity;
use Weasty\Money\Price\Price;

/**
 * Class ActionCondition
 * @package Shop\DiscountBundle\Entity
 */
class ActionCondition extends AbstractEntity
    implements ActionConditionInterface
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
        $this->categories = new ArrayCollection();
        $this->discountCategories = new ArrayCollection();
        $this->proposals = new ArrayCollection();
        $this->discountProposals = new ArrayCollection();
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
     * Add categories
     *
     * @param \Shop\CatalogBundle\Entity\Category $categories
     * @return ActionCondition
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
     * Add proposals
     *
     * @param \Shop\CatalogBundle\Entity\Proposal $proposals
     * @return ActionCondition
     */
    public function addProposal(Proposal $proposals)
    {
        $this->proposals[] = $proposals;

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
    private $gifts;


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
     * Add gifts
     *
     * @param \Shop\CatalogBundle\Entity\Proposal $gift
     * @return ActionCondition
     */
    public function addGift(Proposal $gift)
    {
        $this->gifts[] = $gift;
        return $this;
    }

    /**
     * Remove gifts
     *
     * @param \Shop\CatalogBundle\Entity\Proposal $gift
     */
    public function removeGift(Proposal $gift)
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
     * Add discountCategories
     *
     * @param \Shop\CatalogBundle\Entity\Category $discountCategory
     * @return ActionCondition
     */
    public function addDiscountCategory(Category $discountCategory)
    {
        $this->discountCategories[] = $discountCategory;

        return $this;
    }

    /**
     * Remove discountCategories
     *
     * @param \Shop\CatalogBundle\Entity\Category $discountCategory
     */
    public function removeDiscountCategory(Category $discountCategory)
    {
        $this->discountCategories->removeElement($discountCategory);
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
     * Add discountProposals
     *
     * @param \Shop\CatalogBundle\Entity\Proposal $discountProposal
     * @return ActionCondition
     */
    public function addDiscountProposal(Proposal $discountProposal)
    {
        $this->discountProposals[] = $discountProposal;

        return $this;
    }

    /**
     * Remove discountProposals
     *
     * @param \Shop\CatalogBundle\Entity\Proposal $discountProposal
     */
    public function removeDiscountProposal(Proposal $discountProposal)
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
     * @ORM\PreFlush
     */
    public function mergeDiscountProposals()
    {
        /**
         * @var $discountProposals \Shop\CatalogBundle\Entity\Proposal[]
         */
        $proposals = $this->getProposals();
        $discountProposals = $this->getDiscountProposals();
        foreach($discountProposals as $discountProposal){
            if(!$proposals->contains($discountProposal)){
                $this->removeDiscountProposal($discountProposal);
            }
        }
    }

    /**
     * @ORM\PreFlush
     */
    public function mergeDiscountCategories()
    {
        /**
         * @var $discountCategories \Shop\CatalogBundle\Entity\Category[]
         */
        $categories = $this->getCategories();
        $discountCategories = $this->getCategories();
        foreach($discountCategories as $discountCategory){
            if(!$categories->contains($discountCategory)){
                $this->removeDiscountCategory($discountCategory);
            }
        }
    }
}
