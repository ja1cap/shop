<?php
namespace Weasty\MoneyBundle\Data;

/**
 * Interface CurrencyInterface
 * @package Weasty\MoneyBundle\Data
 */
interface CurrencyInterface {

    /**
     * @return string|null
     */
    public function getSymbol();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return integer|string
     */
    public function getNumericCode();

    /**
     * @return integer|string
     */
    public function getAlphabeticCode();

} 