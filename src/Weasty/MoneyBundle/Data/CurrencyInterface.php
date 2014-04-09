<?php
namespace Weasty\MoneyBundle\Data;

/**
 * Interface CurrencyInterface
 * @package Weasty\MoneyBundle\Data
 */
interface CurrencyInterface {

    /**
     * @return integer|string
     */
    public function getNumericCode();

    /**
     * @return integer|string
     */
    public function getAlphabeticCode();

} 