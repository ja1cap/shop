<?php
namespace Shop\ShippingBundle\Entity;

use Weasty\Doctrine\Entity\AbstractEntity;
use Weasty\Money\Price\PriceInterface;

/**
 * Class ShippingAssemblyPrice
 * @package Shop\ShippingBundle\Entity
 */
abstract class ShippingAssemblyPrice extends AbstractEntity
    implements PriceInterface
{

    /**
     * @var integer
     */
    private $value;

    /**
     * @var integer
     */
    private $currencyNumericCode;


    /**
     * Set value
     *
     * @param integer $value
     * @return ShippingAssemblyPrice
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return integer 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set currencyNumericCode
     *
     * @param integer $currencyNumericCode
     * @return ShippingAssemblyPrice
     */
    public function setCurrencyNumericCode($currencyNumericCode)
    {
        $this->currencyNumericCode = $currencyNumericCode;

        return $this;
    }

    /**
     * Get currencyNumericCode
     *
     * @return integer 
     */
    public function getCurrencyNumericCode()
    {
        return $this->currencyNumericCode;
    }

    /**
     * @return integer|string|\Weasty\Money\Currency\CurrencyInterface
     */
    public function getCurrency()
    {
        return $this->getCurrencyNumericCode();
    }

}
