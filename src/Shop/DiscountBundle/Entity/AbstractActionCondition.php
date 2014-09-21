<?php
namespace Shop\DiscountBundle\Entity;

use Shop\DiscountBundle\ActionCondition\ActionConditionData;
use Shop\DiscountBundle\ActionCondition\ActionConditionInterface;
use Weasty\Bundle\DoctrineBundle\Entity\AbstractEntity;
use Weasty\Doctrine\Cache\Collection\CacheCollectionEntityInterface;
use Weasty\Money\Price\Price;

/**
 * Class AbstractActionCondition
 * @package Shop\DiscountBundle\Entity
 */
abstract class AbstractActionCondition extends AbstractEntity
    implements  ActionConditionInterface,
                CacheCollectionEntityInterface
{

    /**
     * @var integer
     */
    protected $actionId;

    /**
     * @var integer
     */
    protected $type;

    /**
     * @var float
     */
    protected $discountPercent;

    /**
     * @var float
     */
    protected $discountPriceValue;

    /**
     * @var integer
     */
    protected $discountPriceCurrencyNumericCode;

    /**
     * @var integer
     */
    protected $priority;

    /**
     * @var boolean
     */
    protected $isComplex;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->priority = 1;
        $this->type = self::TYPE_INHERIT;
        $this->isComplex = false;
    }

    /**
     * @return string
     */
    function __toString()
    {
        return (string)$this->getId();
    }

    /**
     * @return boolean
     */
    public function getIsGiftCondition()
    {
        return in_array($this->getType(), ActionConditionData::getGiftTypes());
    }

    /**
     * @return boolean
     */
    public function getIsDiscountCondition()
    {
        return in_array($this->getType(), ActionConditionData::getDiscountTypes());
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
     * Set type
     *
     * @param integer $discountType
     * @return ActionCondition
     */
    public function setType($discountType)
    {
        $this->type = $discountType;

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
     * @return ActionConditionInterface
     */
    public function getParentCondition()
    {
        return ($this->getType() == self::TYPE_INHERIT && $this->getAction() ? $this->getAction()->getBasicCondition() : null);
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
     * Create gift
     *
     * @return \Shop\DiscountBundle\Entity\AbstractActionConditionGift
     */
    abstract public function createGift();

    /**
     * Add gift
     *
     * @param \Shop\DiscountBundle\Entity\AbstractActionConditionGift $gift
     * @return ActionCondition
     */
    abstract public function addGift(AbstractActionConditionGift $gift);

    /**
     * Remove gift
     *
     * @param \Shop\DiscountBundle\Entity\AbstractActionConditionGift $gift
     */
    abstract public function removeGift(AbstractActionConditionGift $gift);

    /**
     * Get gift proposals
     * @return \Shop\CatalogBundle\Proposal\ProposalInterface[]
     */
    public function getGiftProposals()
    {
        $proposals = [];
        foreach ($this->getGifts() as $gift) {
            $proposals[] = $gift->getProposal();
        }
        return $proposals;
    }

    /**
     * Get \Shop\CatalogBundle\Entity\Proposal ids
     * @return array
     */
    public function getGiftProposalIds(){
        $proposalIds = [];
        foreach ($this->getGifts() as $gift) {
            $proposalIds[] = $gift->getProposalId();
        }
        return $proposalIds;
    }

}
