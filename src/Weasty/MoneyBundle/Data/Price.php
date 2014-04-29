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
    function __construct($value = null, $currency = null)
    {
        $this->value = $value;
        $this->currency = $currency;
    }

    /**
     * @param int|string|CurrencyInterface $currency
     * @return $this
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @param float|int|string $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
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

    /**
     * @return string
     */
    function __toString()
    {
        return (string)$this->getValue();
    }

}