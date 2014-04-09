<?php
namespace Weasty\MoneyBundle\Converter;

/**
 * Interface CurrencyCodeConverterInterface
 * @package Weasty\MoneyBundle\Converter
 */
interface CurrencyCodeConverterInterface {

    /**
     * Get currency code type
     *
     * @param string|integer|\Weasty\MoneyBundle\Data\CurrencyInterface $currencyCode
     * @return integer|string
     */
    public function getCurrencyCodeType($currencyCode);

    /**
     * @param integer|string|\Weasty\MoneyBundle\Data\CurrencyInterface $currency Source currency code
     * @param integer|string $destinationCodeType Destination currency code type
     * @param null|integer|string $sourceCodeType Source currency code type
     * @return mixed
     */
    public function convert($currency, $destinationCodeType, $sourceCodeType = null);

} 