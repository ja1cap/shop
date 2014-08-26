<?php
namespace Shop\CatalogBundle\Currency;

use Shop\CatalogBundle\Entity\ContractorCurrency;
use Shop\CatalogBundle\Entity\Price;
use Weasty\Money\Currency\Converter\CurrencyConverter;
use Weasty\Money\Currency\CurrencyResource;

/**
 * Class PriceCurrencyConverter
 * @package Shop\CatalogBundle\Currency
 */
class PriceCurrencyConverter extends CurrencyConverter {

    /**
     * @param string|integer|float|\Weasty\Money\Price\PriceInterface $price
     * @param string|integer|\Weasty\Money\Currency\CurrencyInterface|null $sourceCurrency
     * @param string|integer|\Weasty\Money\Currency\CurrencyInterface|null $destinationCurrency
     * @return string|integer|float|null
     */
    public function convert($price, $sourceCurrency = null, $destinationCurrency = null)
    {

        if(!$price instanceof Price){
            return parent::convert($price, $sourceCurrency, $destinationCurrency);
        }

        $sourceCurrencyAlphabeticCode = $this
            ->getCurrencyCodeConverter()
            ->convert(
                $price->getCurrency(),
                CurrencyResource::CODE_TYPE_ISO_4217_ALPHABETIC
            );

        $destinationCurrency = $destinationCurrency ?: $this->getCurrencyResource()->getDefaultCurrency();

        $destinationCurrencyAlphabeticCode = $this
            ->getCurrencyCodeConverter()
            ->convert(
                $destinationCurrency,
                CurrencyResource::CODE_TYPE_ISO_4217_ALPHABETIC
            );

        if ($destinationCurrencyAlphabeticCode != $sourceCurrencyAlphabeticCode) {

            $exchangedContractorValue = null;

            $priceCurrencyNumericCode = $price->getCurrencyNumericCode();

            if($price->getContractor()){

                $currency = $price->getContractor()->getCurrencies()->filter(function(ContractorCurrency $currency) use ($priceCurrencyNumericCode) {
                    return $currency->getNumericCode() == $priceCurrencyNumericCode;
                })->current();

                if($currency instanceof ContractorCurrency){
                    $exchangedContractorValue = $price->getValue() * $currency->getValue();
                }

            }

            if($exchangedContractorValue){

                $value = $exchangedContractorValue;

            } else {

                $value = $this->exchange($price->getValue(), $sourceCurrencyAlphabeticCode, $destinationCurrencyAlphabeticCode);

            }

        } else {

            $value = $price->getValue();

        }

        return $value;

    }

} 