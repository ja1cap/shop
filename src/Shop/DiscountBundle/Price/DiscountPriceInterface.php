<?php
namespace Shop\DiscountBundle\Price;

/**
 * Interface DiscountPriceInterface
 * @package Shop\DiscountBundle\Price
 */
interface DiscountPriceInterface {

    /**
     * @return \Weasty\Money\Price\PriceInterface
     */
    public function getOriginalPrice();

    /**
     * @return float|null
     */
    public function getDiscountPercent();

} 