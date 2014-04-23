<?php
namespace Shop\ShippingBundle\Calculator;

/**
 * Interface ShippingSummaryInterface
 * @package Shop\ShippingBundle\Calculator
 */
interface ShippingSummaryInterface {

    /**
     * @return \Weasty\MoneyBundle\Data\PriceInterface|null
     */
    public function getSummaryPrice();

    /**
     * @return \Weasty\MoneyBundle\Data\PriceInterface|null
     */
    public function getShippingPrice();

    /**
     * @return \Weasty\MoneyBundle\Data\PriceInterface|null
     */
    public function getLiftingPrice();

    /**
     * @return \Weasty\MoneyBundle\Data\PriceInterface|null
     */
    public function getAssemblyPrice();

} 