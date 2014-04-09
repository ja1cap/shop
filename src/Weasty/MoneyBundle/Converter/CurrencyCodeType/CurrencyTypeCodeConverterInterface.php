<?php
namespace Weasty\MoneyBundle\Converter\CurrencyCodeType;

/**
 * Interface CurrencyTypeCodeConverterInterface
 * @package Weasty\MoneyBundle\Converter\CurrencyCodeType
 */
interface CurrencyTypeCodeConverterInterface {

    /**
     * Currency type unique identifier
     * @return string|integer
     */
    static function getType();

    /**
     * Check is currency code is valid for current type
     * @param $currencyCode
     * @return boolean
     */
    function isValidCurrencyCode($currencyCode);

    /**
     * @param $currencyCode
     * @return string The 3-letter ISO 4217 currency code
     */
    function getAlphabeticCurrencyCode($currencyCode);

    /**
     * @param string $currencyAlphabeticCode The 3-letter ISO 4217 currency code
     * @return mixed
     */
    function getTypeCurrencyCode($currencyAlphabeticCode);

} 