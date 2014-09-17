<?php
namespace Shop\DiscountBundle\Price;

use Weasty\Money\Price\Price;

/**
 * Class DiscountPrice
 * @package Shop\DiscountBundle\Price
 */
class DiscountPrice extends Price implements DiscountPriceInterface {

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

            $perPercent = ($this->getOriginalPrice()->getValue() / 100);
            $diff = ($this->getOriginalPrice()->getValue() - $this->getValue());

            if($diff >= $perPercent){
                $discountPercent = ($diff / $perPercent);
                $this->discountPercent = round(floatval($discountPercent));
            } else {
                $this->discountPercent = 0;
            }

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