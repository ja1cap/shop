<?php
namespace Shop\CatalogBundle\Converter;

use Shop\CatalogBundle\Entity\ContractorCurrency;
use Shop\CatalogBundle\Entity\Price;
use Weasty\MoneyBundle\Converter\CurrencyConverter;
use Weasty\MoneyBundle\Data\CurrencyResource;

/**
 * Class ShopPriceConverter
 * @package Shop\CatalogBundle\Converter
 */
class ShopPriceCurrencyConverter extends CurrencyConverter {

    /**
     * @param string|integer|float|\Weasty\MoneyBundle\Data\PriceInterface $price
     * @param string|integer|\Weasty\MoneyBundle\Data\CurrencyInterface $sourceCurrency
     * @param string|integer|\Weasty\MoneyBundle\Data\CurrencyInterface $destinationCurrency
     * @return string|integer|float|null
     */
    public function convert($price, $sourceCurrency, $destinationCurrency)
    {

        if(!$price instanceof Price){
            return null;
        }

        $sourceCurrencyAlphabeticCode = $this
            ->getCurrencyCodeConverter()
            ->convert(
                $price->getCurrency(),
                CurrencyResource::CODE_TYPE_ISO_4217_ALPHABETIC
            );

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