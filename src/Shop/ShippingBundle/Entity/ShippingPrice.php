<?php

namespace Shop\ShippingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Shop\CatalogBundle\Entity\ContractorCurrency;
use Weasty\DoctrineBundle\Entity\AbstractEntity;
use Weasty\ResourceBundle\Data\PriceInterface;

/**
 * Class ShippingPrice
 * @package Shop\ShippingBundle\Entity
 */
abstract class ShippingPrice extends AbstractEntity
    implements PriceInterface
{

    const ORDER_PRICE_TYPE_ANY = 1;
    const ORDER_PRICE_TYPE_RANGE = 2;

    /**
     * @var integer
     */
    protected $orderPriceType;

    /**
     * @var integer
     */
    protected $minOrderPrice;

    /**
     * @var integer
     */
    protected $maxOrderPrice;

    /**
     * @var integer
     */
    protected $value;

    function __construct()
    {
        $this->orderPriceType = self::ORDER_PRICE_TYPE_ANY;
    }

    /**
     * Set orderPriceType
     *
     * @param integer $orderPriceType
     * @return ShippingPrice
     */
    public function setOrderPriceType($orderPriceType)
    {
        $this->orderPriceType = $orderPriceType;

        return $this;
    }

    /**
     * Get orderPriceType
     *
     * @return integer 
     */
    public function getOrderPriceType()
    {
        return $this->orderPriceType ?: self::ORDER_PRICE_TYPE_ANY;
    }

    /**
     * Set minOrderPrice
     *
     * @param integer $minOrderPrice
     * @return ShippingPrice
     */
    public function setMinOrderPrice($minOrderPrice)
    {
        $this->minOrderPrice = $minOrderPrice;

        return $this;
    }

    /**
     * Get minOrderPrice
     *
     * @return integer 
     */
    public function getMinOrderPrice()
    {
        return $this->minOrderPrice;
    }

    /**
     * Set maxOrderPrice
     *
     * @param integer $maxOrderPrice
     * @return ShippingPrice
     */
    public function setMaxOrderPrice($maxOrderPrice)
    {
        $this->maxOrderPrice = $maxOrderPrice;

        return $this;
    }

    /**
     * Get maxOrderPrice
     *
     * @return integer 
     */
    public function getMaxOrderPrice()
    {
        return $this->maxOrderPrice;
    }

    /**
     * Set value
     *
     * @param integer $value
     * @return ShippingPrice
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
     * @var integer
     */
    private $currencyNumericCode;


    /**
     * Set currencyNumericCode
     *
     * @param integer $currencyNumericCode
     * @return ShippingPrice
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
     * @return bool
     */
    public function getCurrencyShortName(){
        if(!isset(ContractorCurrency::$currencyShortNames[$this->getCurrencyNumericCode()])){
            return false;
        }
        return ContractorCurrency::$currencyShortNames[$this->getCurrencyNumericCode()];
    }
}
