<?php
namespace Shop\ShippingBundle\Calculator;

/**
 * Interface ShippingCalculatorInterface
 * @package Shop\ShippingBundle\Calculator
 */
interface ShippingCalculatorInterface {

    /**
     * @param $options
     * @return ShippingCalculatorResultInterface
     */
    public function calculate($options = null);

} 