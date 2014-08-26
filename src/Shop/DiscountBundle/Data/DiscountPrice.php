<?php
namespace Shop\DiscountBundle\Data;

use Weasty\Money\Price\Price;

/**
 * Class DiscountPrice
 * @package Shop\DiscountBundle\Data
 */
class DiscountPrice extends Price {

    /**
     * @var \Weasty\Money\Price\PriceInterface
     */
    protected $originalPrice;

    /**
     * @var null|float
     */
    protected $discountPercent;

    /**
     * @var \Shop\DiscountBundle\Entity\ActionConditionInterface
     */
    protected $discountCondition;

    /**
     * @return \Weasty\Money\Price\PriceInterface
     */
    public function getOriginalPrice()
    {
        return $this->originalPrice;
    }

    /**
     * @param \Weasty\Money\Price\PriceInterface $originalPrice
     * @return $this
     */
    public function setOriginalPrice($originalPrice)
    {
        $this->originalPrice = $originalPrice;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getDiscountPercent()
    {
        if($this->discountPercent === null){
            $this->discountPercent = floatval($this->getOriginalPrice()->getValue() / $this->getValue());
        }
        return $this->discountPercent;
    }


    /**
     * @param float|null $discountPercent
     * @return $this
     */
    public function setDiscountPercent($discountPercent)
    {
        $this->discountPercent = $discountPercent;
        return $this;
    }

    /**
     * @return \Shop\DiscountBundle\Entity\ActionConditionInterface
     */
    public function getDiscountCondition()
    {
        return $this->discountCondition;
    }

    /**
     * @param \Shop\DiscountBundle\Entity\ActionConditionInterface $discountCondition
     * @return $this
     */
    public function setDiscountCondition($discountCondition)
    {
        $this->discountCondition = $discountCondition;
        return $this;
    }

} 