<?php
namespace Shop\ShippingBundle\Calculator;

/**
 * Interface ShippingSummaryInterface
 * @package Shop\ShippingBundle\Calculator
 */
interface ShippingSummaryInterface {

    /**
     * @return \Weasty\Money\Price\PriceInterface|null
     */
    public function getSummaryPrice();

    /**
     * @return \Weasty\Money\Price\PriceInterface|null
     */
    public function getShippingPrice();

    /**
     * @return \Weasty\Money\Price\PriceInterface|null
     */
    public function getLiftingPrice();

    /**
     * @return \Weasty\Money\Price\PriceInterface|null
     */
    public function getAssemblyPrice();

} 