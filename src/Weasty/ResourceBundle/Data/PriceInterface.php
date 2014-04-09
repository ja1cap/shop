<?php
namespace Weasty\ResourceBundle\Data;

/**
 * Interface PriceInterface
 * @package Weasty\ResourceBundle\Data
 */
interface PriceInterface {

    /**
     * @return integer|float|string
     */
    public function getValue();

    /**
     * @return integer|string
     */
    public function getCurrencyNumericCode();

} 