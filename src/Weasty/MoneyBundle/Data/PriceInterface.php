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
     * @return integer|string|CurrencyInterface
     */
    public function getCurrency();

} 