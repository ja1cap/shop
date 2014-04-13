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

        $this->defaultCurrency = $defaultCurrency;
        $this->locale = $locale;

        $this->currencies = array();

        if(is_array($currencies)){
            foreach($currencies as $alphabeticCode => $data){
                $this->currencies[$alphabeticCode] = $this->buildCurrency($alphabeticCode, $data);
            }
        }

    }

    /**
     * @param $currencyAlphabeticCode
     * @param array $data
     * @return Currency
     */
    protected function buildCurrency($currencyAlphabeticCode, array $data = array()){

        $currency = new Currency($data);
        $currency->setAlphabeticCode($currencyAlphabeticCode);

        if(!$currency->getName()){
            $name = Intl::getCurrencyBundle()->getCurrencyName($currencyAlphabeticCode, $this->getLocale());
            $currency->setName($name);
        }

        return $currency;

    }

    /**
     * @param $currencyAlphabeticCode
     * @return null|Currency|array
     */
    public function getCurrency($currencyAlphabeticCode){
        if(isset($this->currencies[$currencyAlphabeticCode])){
            return $this->currencies[$currencyAlphabeticCode];
        }
        return null;
    }

    /**
     * @param $currencyAlphabeticCode
     * @param $parameterName
     * @return mixed
     */
    public function getCurrencyParameter($currencyAlphabeticCode, $parameterName){

        $currency = $this->getCurrency($currencyAlphabeticCode);

        if($currency && isset($currency[$parameterName])){
            return $currency[$parameterName];
        }

        return null;

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
        return $this->getCurrencyParameter($currencyAlphabeticCode, 'name') ?: Intl::getCurrencyBundle()->getCurrencyName($currencyAlphabeticCode, $this->getLocale());
    }

    /**
     * @param $currencyAlphabeticCode
     * @return null|string
     */
    public function getCurrencySymbol($currencyAlphabeticCode){
        return $this->getCurrencyParameter($currencyAlphabeticCode, 'symbol') ?: Intl::getCurrencyBundle()->getCurrencySymbol($currencyAlphabeticCode, $this->getLocale());
    }

    /**
     * @param $currencyAlphabeticCode
     * @return null|integer
     */
    public function getCurrencyNumericCode($currencyAlphabeticCode){
        return $this->getCurrencyParameter($currencyAlphabeticCode, 'numericCode');
    }

    /**
     * @param $currencyNumericCode
     * @return null|string
     */
    public function getCurrencyAlphabeticCode($currencyNumericCode){

        foreach($this->getCurrencies() as $currencyAlphabeticCode => $currency){
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