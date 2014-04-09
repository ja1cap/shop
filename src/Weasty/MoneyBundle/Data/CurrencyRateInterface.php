<?php
namespace Weasty\MoneyBundle\Data;

/**
 * Interface CurrencyRateInterface
 * @package Weasty\MoneyBundle\Data
 */
interface CurrencyRateInterface {

    /**
     * Get destinationAlphabeticCode
     *
     * @return string
     */
    public function getDestinationAlphabeticCode();

    /**
     * @return string
     */
    public function getSourceAlphabeticCode();

    /**
     * @return float
     */
    public function getRate();

} 