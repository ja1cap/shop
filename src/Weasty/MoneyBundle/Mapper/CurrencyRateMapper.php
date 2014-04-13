<?php
namespace Weasty\MoneyBundle\Mapper;

use Weasty\MoneyBundle\Data\CurrencyResource;
use Weasty\MoneyBundle\Entity\CurrencyRate;
use Weasty\MoneyBundle\Converter\CurrencyCodeConverterInterface;

/**
 * Class CurrencyRateMapper
 * @package Weasty\MoneyBundle\Mapper
 */
class CurrencyRateMapper {

    /**
     * @var \Weasty\MoneyBundle\Entity\CurrencyRate
     */
    protected $currencyRate;

    /**
     * @var CurrencyCodeConverterInterface
     */
    protected $currencyCodeConverter;

    /**
     * @param CurrencyRate $currencyRate
     * @param CurrencyCodeConverterInterface $currencyCodeConverter
     */
    function __construct(CurrencyRate $currencyRate, CurrencyCodeConverterInterface $currencyCodeConverter)
    {
        $this->currencyRate = $currencyRate;
        $this->currencyCodeConverter = $currencyCodeConverter;
    }

    /**
     * @return CurrencyRate
     */
    public function getCurrencyRate()
    {
        return $this->currencyRate;
    }

    /**
     * @param $rate
     * @return $this
     */
    public function setRate($rate){
        $this->getCurrencyRate()->setRate($rate);
        return $this;
    }

    /**
     * @return float
     */
    public function getRate(){
        return $this->getCurrencyRate()->getRate();
    }

    /**
     * @param $alphabeticCode
     * @return $this
     */
    public function setSourceAlphabeticCode($alphabeticCode){

        $numericCode = $this->getCurrencyCodeConverter()->convert($alphabeticCode, CurrencyResource::CODE_TYPE_ISO_4217_NUMERIC);

        $this->getCurrencyRate()
            ->setSourceAlphabeticCode($alphabeticCode)
            ->setSourceNumericCode($numericCode);

        return $this;

    }

    /**
     * @return string
     */
    public function getSourceAlphabeticCode(){
        return $this->getCurrencyRate()->getSourceAlphabeticCode();
    }

    /**
     * @param $alphabeticCode
     * @return $this
     */
    public function setDestinationAlphabeticCode($alphabeticCode){

        $numericCode = $this->getCurrencyCodeConverter()->convert($alphabeticCode, CurrencyResource::CODE_TYPE_ISO_4217_NUMERIC);

        $this->getCurrencyRate()
            ->setDestinationAlphabeticCode($alphabeticCode)
            ->setDestinationNumericCode($numericCode);

        return $this;

    }

    /**
     * @return string
     */
    public function getDestinationAlphabeticCode(){
        return $this->getCurrencyRate()->getDestinationAlphabeticCode();
    }

    /**
     * @return CurrencyCodeConverterInterface
     */
    public function getCurrencyCodeConverter()
    {
        return $this->currencyCodeConverter;
    }

} 