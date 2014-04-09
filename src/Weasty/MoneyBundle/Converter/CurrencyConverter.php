<?php
namespace Weasty\MoneyBundle\Converter;

use Doctrine\Common\Persistence\ObjectRepository;
use Weasty\MoneyBundle\Data\CurrencyRateInterface;
use Weasty\MoneyBundle\Data\CurrencyResource;
use Weasty\MoneyBundle\Data\PriceInterface;

/**
 * Class CurrencyConverter
 * @package Weasty\MoneyBundle\Converter
 */
class CurrencyConverter implements CurrencyConverterInterface {

    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository
     */
    protected $currencyRateRepository;

    /**
     * @var \Weasty\MoneyBundle\Converter\CurrencyCodeConverterInterface
     */
    protected $currencyCodeConverter;

    /**
     * @param $currencyRateRepository
     * @param $currencyCodeConverter
     */
    function __construct(ObjectRepository $currencyRateRepository, CurrencyCodeConverterInterface $currencyCodeConverter)
    {
        $this->currencyRateRepository = $currencyRateRepository;
        $this->currencyCodeConverter = $currencyCodeConverter;
    }

    /**
     * @param string|integer|float|\Weasty\MoneyBundle\Data\PriceInterface $value
     * @param string|integer|\Weasty\MoneyBundle\Data\CurrencyInterface $sourceCurrency
     * @param string|integer|\Weasty\MoneyBundle\Data\CurrencyInterface $destinationCurrency
     * @return string|integer|float|null
     */
    public function convert($value, $sourceCurrency, $destinationCurrency)
    {

        if($value instanceof PriceInterface){
            $value = $value->getValue();
        }

        $sourceCurrencyAlphabeticCode = $this
            ->getCurrencyCodeConverter()
            ->convert(
                $sourceCurrency,
                CurrencyResource::CODE_TYPE_ISO_4217_ALPHABETIC
            );

        $destinationCurrencyAlphabeticCode = $this
            ->getCurrencyCodeConverter()
            ->convert(
                $destinationCurrency,
                CurrencyResource::CODE_TYPE_ISO_4217_ALPHABETIC
            );

        if($destinationCurrencyAlphabeticCode != $sourceCurrencyAlphabeticCode) {

            $value = $this->exchange($value, $sourceCurrencyAlphabeticCode, $destinationCurrencyAlphabeticCode);

        }

        return $value;

    }

    /**
     * @param $value
     * @param $sourceCurrencyAlphabeticCode
     * @param $destinationCurrencyAlphabeticCode
     * @return float|integer|string
     */
    protected function exchange($value, $sourceCurrencyAlphabeticCode, $destinationCurrencyAlphabeticCode){

        $currencyRate = $this->getCurrencyRateRepository()->findOneBy(array(
            'sourceAlphabeticCode' => $sourceCurrencyAlphabeticCode,
            'destinationAlphabeticCode' => $destinationCurrencyAlphabeticCode,
        ));

        if($currencyRate instanceof CurrencyRateInterface){
            $value = ($value * $currencyRate->getRate());
        }

        return $value;

    }

    /**
     * @return \Weasty\MoneyBundle\Converter\CurrencyCodeConverterInterface
     */
    public function getCurrencyCodeConverter()
    {
        return $this->currencyCodeConverter;
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    protected function getCurrencyRateRepository()
    {
        return $this->currencyRateRepository;
    }

} 