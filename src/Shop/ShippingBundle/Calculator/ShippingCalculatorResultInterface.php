<?php
namespace Shop\ShippingBundle\Calculator;

/**
 * Interface ShippingCalculatorResultInterface
 * @package Shop\ShippingBundle\Calculator
 */
interface ShippingCalculatorResultInterface {

    /**
     * @return \Weasty\Money\Price\PriceInterface
     */
    public function getSummaryPrice();

} 