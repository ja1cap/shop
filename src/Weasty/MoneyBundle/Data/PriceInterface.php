<?php
namespace Weasty\MoneyBundle\Data;

/**
 * Interface PriceInterface
 * @package Weasty\MoneyBundle\Data
 */
interface PriceInterface {

    /**
     * @return integer|float|string
     */
    public function getValue();

    /**
     * @return integer|string|\Weasty\MoneyBundle\Data\CurrencyInterface
     */
    public function getCurrency();

} 