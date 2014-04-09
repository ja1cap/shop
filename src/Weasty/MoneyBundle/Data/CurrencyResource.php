<?php
namespace Weasty\MoneyBundle\Data;
use Symfony\Component\Intl\Intl;

/**
 * Class CurrencyResource
 * @package Weasty\MoneyBundle\Data
 */
class CurrencyResource {

    const CODE_TYPE_ISO_4217_NUMERIC = 'ISO-4217-NUM';
    const CODE_TYPE_ISO_4217_ALPHABETIC = 'ISO-4217-ALPHA';

    /**
     * @var array
     */
    protected $currencies;

    /**
     * @var string
     */
    protected $defaultCurrency;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @param $currencies
     * @param $defaultCurrency
     * @param $locale
     */
    function __construct($currencies, $defaultCurrency, $locale)
    {
        $this->currencies = $currencies;
        $this->defaultCurrency = $defaultCurrency;
        $this->locale = $locale;
    }

    /**
     * @return array
     */
    public function getCurrencies()
    {
        return $this->currencies;
    }

    /**
     * @return array
     */
    public function getCurrencyAlphabeticCodes(){
        return array_keys($this->getCurrencies());
    }

    /**
     * @param $currencyAlphabeticCode
     * @return null|string
     */
    public function getCurrencyName($currencyAlphabeticCode){
        if(isset($this->currencies[$currencyAlphabeticCode]) && isset($this->currencies[$currencyAlphabeticCode]['name'])){
            return $this->currencies[$currencyAlphabeticCode]['name'];
        }
        return Intl::getCurrencyBundle()->getCurrencyName($currencyAlphabeticCode, $this->getLocale());
    }

    /**
     * @param $currencyAlphabeticCode
     * @return null|string
     */
    public function getCurrencySymbol($currencyAlphabeticCode){
        if(isset($this->currencies[$currencyAlphabeticCode]) && isset($this->currencies[$currencyAlphabeticCode]['symbol'])){
            return $this->currencies[$currencyAlphabeticCode]['symbol'];
        }
        return Intl::getCurrencyBundle()->getCurrencySymbol($currencyAlphabeticCode, $this->getLocale());
    }

    /**
     * @param $currencyAlphabeticCode
     * @return null|integer
     */
    public function getCurrencyNumericCode($currencyAlphabeticCode){
        if(isset($this->currencies[$currencyAlphabeticCode]) && isset($this->currencies[$currencyAlphabeticCode]['numericCode'])){
            return $this->currencies[$currencyAlphabeticCode]['numericCode'];
        }
        return null;
    }

    /**
     * @param $currencyNumericCode
     * @return null|string
     */
    public function getCurrencyAlphabeticCode($currencyNumericCode){

        foreach($this->currencies as $currencyAlphabeticCode => $currency){
            if(isset($currency['numericCode']) && $currency['numericCode'] == $currencyNumericCode){
                return $currencyAlphabeticCode;
                break;
            }
        }

        return null;

    }

    /**
     * @return string
     */
    public function getDefaultCurrency()
    {
        return $this->defaultCurrency;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

} 