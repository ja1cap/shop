<?php
namespace Weasty\MoneyBundle\Converter;

/**
 * Interface CurrencyConverterInterface
 * @package Weasty\MoneyBundle\Converter
 */
interface CurrencyConverterInterface {

    /**
     * @param string|integer|float|\Weasty\MoneyBundle\Data\PriceInterface $value
     * @param string|integer|\Weasty\MoneyBundle\Data\CurrencyInterface|null $sourceCurrency
     * @param string|integer|\Weasty\MoneyBundle\Data\CurrencyInterface|null $destinationCurrency
     * @return string|integer|float|null
     */
    public function convert($value, $sourceCurrency = null, $destinationCurrency = null);

    /**
     * @return \Weasty\MoneyBundle\Data\CurrencyResource
     */
    public function getCurrencyResource();

} 