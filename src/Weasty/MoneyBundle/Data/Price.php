<?php
namespace Weasty\MoneyBundle\Data;

/**
 * Class Price
 * @package Weasty\MoneyBundle\Data
 */
class Price implements PriceInterface {

    /**
     * @var integer|float|string
     */
    protected $value;

    /**
     * @var integer|string|\Weasty\MoneyBundle\Data\CurrencyInterface
     */
    protected $currency;

    /**
     * @param integer|float|string $value
     * @param integer|string|\Weasty\MoneyBundle\Data\CurrencyInterface $currency
     */
    function __construct($value, $currency)
    {
        $this->value = $value;
        $this->currency = $currency;
    }

    /**
     * @param int|string|CurrencyInterface $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * @param float|int|string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return integer|float|string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return integer|string|\Weasty\MoneyBundle\Data\CurrencyInterface
     */
    public function getCurrency()
    {
        return $this->currency;
    }

} 