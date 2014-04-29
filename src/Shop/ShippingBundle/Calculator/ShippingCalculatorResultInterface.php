<?php
namespace Shop\ShippingBundle\Calculator;

/**
 * Interface ShippingCalculatorResultInterface
 * @package Shop\ShippingBundle\Calculator
 */
interface ShippingCalculatorResultInterface {

    /**
     * @return \Weasty\MoneyBundle\Data\PriceInterface
     */
    public function getSummaryPrice();

} 