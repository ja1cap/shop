<?php

namespace Shop\ShippingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Weasty\DoctrineBundle\Entity\AbstractEntity;
use Weasty\MoneyBundle\Data\PriceInterface;

/**
 * Class ShippingPrice
 * @package Shop\ShippingBundle\Entity
 */
abstract class ShippingPrice extends AbstractEntity
    implements PriceInterface
{

    const ORDER_PRICE_TYPE_ANY = 1;
    const ORDER_PRICE_TYPE_RANGE = 2;

    const LIFTING_TYPE_BASIC = 0;
    const LIFTING_TYPE_INCLUDED = 1;
    const LIFTING_TYPE_IGNORE = 2;

    const ASSEMBLY_TYPE_BASIC = 0;
    const ASSEMBLY_TYPE_INCLUDED = 1;
    const ASSEMBLY_TYPE_IGNORE = 2;

    /**
     * @var array
     */
    public static $liftingTypes = array(
        self::LIFTING_TYPE_BASIC => 'Базовый тариф',
        self::LIFTING_TYPE_INCLUDED => 'Включено в стоимость доставки',
        self::LIFTING_TYPE_IGNORE => 'Не учитываеть',
    );

    /**
     * @var array
     */
    public static $assemblyTypes = array(
        self::ASSEMBLY_TYPE_BASIC => 'Базовый тариф',
        self::ASSEMBLY_TYPE_INCLUDED => 'Включено в стоимость доставки',
        self::ASSEMBLY_TYPE_IGNORE => 'Не учитываеть',
    );

    /**
     * @var integer
     */
    protected $orderPriceType;

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
     * @return integer|string
     */
    public function getCurrency()
    {
        return $this->getCurrencyNumericCode();
    }
    /**
     * @var integer
     */
    private $minOrderPriceValue;

    /**
     * @var integer
     */
    private $minOrderPriceCurrencyNumericCode;

    /**
     * @var integer
     */
    private $maxOrderPriceValue;

    /**
     * @var integer
     */
    private $maxOrderPriceCurrencyNumericCode;


    /**
     * Set minOrderPriceValue
     *
     * @param integer $minOrderPriceValue
     * @return ShippingPrice
     */
    public function setMinOrderPriceValue($minOrderPriceValue)
    {
        $this->minOrderPriceValue = $minOrderPriceValue;

        return $this;
    }

    /**
     * Get minOrderPriceValue
     *
     * @return integer 
     */
    public function getMinOrderPriceValue()
    {
        return $this->minOrderPriceValue;
    }

    /**
     * Set minOrderPriceCurrencyNumericCode
     *
     * @param integer $minOrderPriceCurrencyNumericCode
     * @return ShippingPrice
     */
    public function setMinOrderPriceCurrencyNumericCode($minOrderPriceCurrencyNumericCode)
    {
        $this->minOrderPriceCurrencyNumericCode = $minOrderPriceCurrencyNumericCode;

        return $this;
    }

    /**
     * Get minOrderPriceCurrencyNumericCode
     *
     * @return integer 
     */
    public function getMinOrderPriceCurrencyNumericCode()
    {
        return $this->minOrderPriceCurrencyNumericCode;
    }

    /**
     * Set maxOrderPriceValue
     *
     * @param integer $maxOrderPriceValue
     * @return ShippingPrice
     */
    public function setMaxOrderPriceValue($maxOrderPriceValue)
    {
        $this->maxOrderPriceValue = $maxOrderPriceValue;

        return $this;
    }

    /**
     * Get maxOrderPriceValue
     *
     * @return integer 
     */
    public function getMaxOrderPriceValue()
    {
        return $this->maxOrderPriceValue;
    }

    /**
     * Set maxOrderPriceCurrencyNumericCode
     *
     * @param integer $maxOrderPriceCurrencyNumericCode
     * @return ShippingPrice
     */
    public function setMaxOrderPriceCurrencyNumericCode($maxOrderPriceCurrencyNumericCode)
    {
        $this->maxOrderPriceCurrencyNumericCode = $maxOrderPriceCurrencyNumericCode;

        return $this;
    }

    /**
     * Get maxOrderPriceCurrencyNumericCode
     *
     * @return integer 
     */
    public function getMaxOrderPriceCurrencyNumericCode()
    {
        return $this->maxOrderPriceCurrencyNumericCode;
    }

    /**
     * @var integer
     */
    private $liftingType;

    /**
     * @var integer
     */
    private $assemblyType;


    /**
     * Set liftingType
     *
     * @param integer $liftingType
     * @return ShippingPrice
     */
    public function setLiftingType($liftingType)
    {
        $this->liftingType = $liftingType;

        return $this;
    }

    /**
     * Get liftingType
     *
     * @return integer 
     */
    public function getLiftingType()
    {
        return $this->liftingType ?: self::LIFTING_TYPE_BASIC;
    }

    /**
     * Set assemblyType
     *
     * @param integer $assemblyType
     * @return ShippingPrice
     */
    public function setAssemblyType($assemblyType)
    {
        $this->assemblyType = $assemblyType;

        return $this;
    }

    /**
     * Get assemblyType
     *
     * @return integer 
     */
    public function getAssemblyType()
    {
        return $this->assemblyType ?: self::ASSEMBLY_TYPE_BASIC;
    }

    /**
     * @return array
     */
    public function getAssemblyTextType()
    {
        switch($this->getAssemblyType()){
            case self::ASSEMBLY_TYPE_INCLUDED:
                $text = 'вкл. в доставку';
                break;
            case self::ASSEMBLY_TYPE_IGNORE:
                $text = 'не используется';
                break;
            case self::ASSEMBLY_TYPE_BASIC:
            default:
                $text = 'базовый тариф';
        }
        return $text;
    }

    /**
     * @return array
     */
    public function getLiftingTextType()
    {
        switch($this->getLiftingType()){
            case self::LIFTING_TYPE_INCLUDED:
                $text = 'вкл. в доставку';
                break;
            case self::LIFTING_TYPE_IGNORE:
                $text = 'не используется';
                break;
            case self::LIFTING_TYPE_BASIC:
            default:
                $text = 'базовый тариф';
        }
        return $text;
    }



}
