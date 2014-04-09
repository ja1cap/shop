<?php
namespace Weasty\MoneyBundle\Converter\CurrencyCodeType;

use Weasty\MoneyBundle\Data\CurrencyResource;

/**
 * Class CurrencyTypeIso4217AlphabeticCodeConverter
 * @package Weasty\MoneyBundle\Converter\CurrencyCodeType
 */
class CurrencyTypeIso4217AlphabeticCodeConverter implements CurrencyTypeCodeConverterInterface {

    function __construct()
    {
    }

    /**
     * Currency type unique identifier
     * @return string|integer
     */
    static function getType()
    {
        return CurrencyResource::CODE_TYPE_ISO_4217_ALPHABETIC;
    }

    /**
     * Check is currency code is valid for current type
     * @param $currencyCode
     * @return boolean
     */
    function isValidCurrencyCode($currencyCode)
    {
        return (preg_match('/^([A-Z]{3})$/', $currencyCode) === 1);
    }

    /**
     * @param $currencyCode
     * @return string The 3-letter ISO 4217 currency code
     */
    function getAlphabeticCurrencyCode($currencyCode)
    {
        return $currencyCode;
    }

    /**
     * @param string $currencyAlphabeticCode The 3-letter ISO 4217 currency code
     * @return mixed
     */
    function getTypeCurrencyCode($currencyAlphabeticCode)
    {
        return $currencyAlphabeticCode;
    }

} 