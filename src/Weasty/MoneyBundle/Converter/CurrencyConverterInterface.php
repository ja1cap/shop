<?php
namespace Weasty\MoneyBundle\Converter;

/**
 * Interface CurrencyConverterInterface
 * @package Weasty\MoneyBundle\Converter
 */
interface CurrencyConverterInterface {

    /**
     * @param string|integer|float|\Weasty\MoneyBundle\Data\PriceInterface $value
     * @param string|integer|\Weasty\MoneyBundle\Data\CurrencyInterface $sourceCurrency
     * @param string|integer|\Weasty\MoneyBundle\Data\CurrencyInterface $destinationCurrency
     * @return string|integer|float|null
     */
    public function convert($value, $sourceCurrency, $destinationCurrency);

} 