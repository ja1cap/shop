<?php
namespace Weasty\MoneyBundle\Twig;

use Symfony\Component\Intl\Intl;
use Weasty\MoneyBundle\Data\CurrencyResource;
use Weasty\MoneyBundle\Data\PriceInterface;
use Weasty\MoneyBundle\Converter\CurrencyCodeConverterInterface;
use Weasty\MoneyBundle\Converter\CurrencyConverterInterface;

/**
 * Class AbstractMoneyExtension
 * @package Weasty\MoneyBundle\Twig
 */
abstract class AbstractMoneyExtension extends \Twig_Extension {

    /**
     * @var CurrencyResource
     */
    protected $currencyResource;

    /**
     * @var \Weasty\MoneyBundle\Converter\CurrencyCodeConverterInterface
     */
    protected $currencyCodeConverter;

    /**
     * @var \Weasty\MoneyBundle\Converter\CurrencyConverterInterface
     */
    protected $currencyConverter;

    /**
     * @param CurrencyResource $currencyResource
     * @param CurrencyCodeConverterInterface $currencyCodeConverter
     * @param CurrencyConverterInterface $currencyConverter
     */
    function __construct(CurrencyResource $currencyResource, CurrencyCodeConverterInterface $currencyCodeConverter, CurrencyConverterInterface $currencyConverter)
    {

        $this->currencyResource = $currencyResource;
        $this->currencyConverter = $currencyConverter;
        $this->currencyCodeConverter = $currencyCodeConverter;

    }

    /**
     * @param string|null|PriceInterface $value
     * @param null $sourceCurrency
     * @param null $destinationCurrency
     * @return string
     */
    public function formatPrice($value, $sourceCurrency = null, $destinationCurrency = null)
    {

        $destinationCurrency = $destinationCurrency ?: $this->getCurrencyResource()->getDefaultCurrency();

        if ($value instanceof PriceInterface) {

            $value = $this
                ->getCurrencyConverter()
                ->convert(
                    $value,
                    $value->getCurrency(),
                    $destinationCurrency
                );

        } else {

            if($sourceCurrency) {

                $value = $this
                    ->getCurrencyConverter()
                    ->convert(
                        $value,
                        $sourceCurrency,
                        $destinationCurrency
                    );

            }

        }

        return $this->formatMoney($value, $destinationCurrency);

    }

    /**
     * @param $value
     * @param $currency
     * @return string
     */
    public function formatMoney($value, $currency){

        $currencyAlphabeticCode = $this
            ->getCurrencyCodeConverter()
            ->convert(
                $currency,
                CurrencyResource::CODE_TYPE_ISO_4217_ALPHABETIC
            );

        $fractionDigits = 0;

        if($fractionDigits === null){
            $currencyBundle = Intl::getCurrencyBundle();
            $fractionDigits = $currencyBundle->getFractionDigits($currencyAlphabeticCode);
        }

        $value = floatval($value);

        $result = number_format($value, $fractionDigits, ',', ' ');

        $currencySymbol = $this->getCurrencyResource()->getCurrencySymbol($currencyAlphabeticCode);
        $prependCurrencySymbol = false;
        if($prependCurrencySymbol){
            $result = $currencySymbol . ' ' . $result;
        } else {
            $result .= ' ' . $currencySymbol;
        }

        return $result;

    }

    /**
     * @return \Weasty\MoneyBundle\Data\CurrencyResource
     */
    public function getCurrencyResource()
    {
        return $this->currencyResource;
    }

    /**
     * @return \Weasty\MoneyBundle\Converter\CurrencyCodeConverterInterface
     */
    public function getCurrencyCodeConverter()
    {
        return $this->currencyCodeConverter;
    }

    /**
     * @return \Weasty\MoneyBundle\Converter\CurrencyConverterInterface
     */
    public function getCurrencyConverter()
    {
        return $this->currencyConverter;
    }

} 