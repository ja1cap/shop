<?php

namespace Shop\ShippingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Weasty\DoctrineBundle\Entity\AbstractEntity;

/**
 * Class ShippingLiftingPrice
 * @package Shop\ShippingBundle\Entity
 */
abstract class ShippingLiftingPrice extends AbstractEntity
{

    const PRICE_TYPE_ANY_FLOOR = 1;
    const PRICE_TYPE_PER_FLOOR = 2;

    /**
     * @var integer
     */
    protected $priceType;

    function __construct()
    {
        $this->priceType = self::PRICE_TYPE_ANY_FLOOR;
    }

    /**
     * Set priceType
     *
     * @param integer $priceType
     * @return ShippingLiftingPrice
     */
    public function setPriceType($priceType)
    {
        $this->priceType = $priceType;

        return $this;
    }

    /**
     * Get priceType
     *
     * @return integer
     */
    public function getPriceType()
    {
        return $this->priceType;
    }

    /**
     * @var integer
     */
    private $noLiftPriceValue;

    /**
     * @var integer
     */
    private $noLiftPriceCurrencyNumericCode;

    /**
     * @var integer
     */
    private $liftPriceValue;

    /**
     * @var integer
     */
    private $liftPriceCurrencyNumericCode;

    /**
     * @var integer
     */
    private $serviceLiftPriceValue;

    /**
     * @var integer
     */
    private $serviceLiftPriceCurrencyNumericCode;


    /**
     * Set noLiftPriceValue
     *
     * @param integer $noLiftPriceValue
     * @return ShippingLiftingPrice
     */
    public function setNoLiftPriceValue($noLiftPriceValue)
    {
        $this->noLiftPriceValue = $noLiftPriceValue;

        return $this;
    }

    /**
     * Get noLiftPriceValue
     *
     * @return integer 
     */
    public function getNoLiftPriceValue()
    {
        return $this->noLiftPriceValue;
    }

    /**
     * Set noLiftPriceCurrencyNumericCode
     *
     * @param integer $noLiftPriceCurrencyNumericCode
     * @return ShippingLiftingPrice
     */
    public function setNoLiftPriceCurrencyNumericCode($noLiftPriceCurrencyNumericCode)
    {
        $this->noLiftPriceCurrencyNumericCode = $noLiftPriceCurrencyNumericCode;

        return $this;
    }

    /**
     * Get noLiftPriceCurrencyNumericCode
     *
     * @return integer 
     */
    public function getNoLiftPriceCurrencyNumericCode()
    {
        return $this->noLiftPriceCurrencyNumericCode;
    }

    /**
     * Set liftPriceValue
     *
     * @param integer $liftPriceValue
     * @return ShippingLiftingPrice
     */
    public function setLiftPriceValue($liftPriceValue)
    {
        $this->liftPriceValue = $liftPriceValue;

        return $this;
    }

    /**
     * Get liftPriceValue
     *
     * @return integer 
     */
    public function getLiftPriceValue()
    {
        return $this->liftPriceValue;
    }

    /**
     * Set liftPriceCurrencyNumericCode
     *
     * @param integer $liftPriceCurrencyNumericCode
     * @return ShippingLiftingPrice
     */
    public function setLiftPriceCurrencyNumericCode($liftPriceCurrencyNumericCode)
    {
        $this->liftPriceCurrencyNumericCode = $liftPriceCurrencyNumericCode;

        return $this;
    }

    /**
     * Get liftPriceCurrencyNumericCode
     *
     * @return integer 
     */
    public function getLiftPriceCurrencyNumericCode()
    {
        return $this->liftPriceCurrencyNumericCode;
    }

    /**
     * Set serviceLiftPriceValue
     *
     * @param integer $serviceLiftPriceValue
     * @return ShippingLiftingPrice
     */
    public function setServiceLiftPriceValue($serviceLiftPriceValue)
    {
        $this->serviceLiftPriceValue = $serviceLiftPriceValue;

        return $this;
    }

    /**
     * Get serviceLiftPriceValue
     *
     * @return integer 
     */
    public function getServiceLiftPriceValue()
    {
        return $this->serviceLiftPriceValue;
    }

    /**
     * Set serviceLiftPriceCurrencyNumericCode
     *
     * @param integer $serviceLiftPriceCurrencyNumericCode
     * @return ShippingLiftingPrice
     */
    public function setServiceLiftPriceCurrencyNumericCode($serviceLiftPriceCurrencyNumericCode)
    {
        $this->serviceLiftPriceCurrencyNumericCode = $serviceLiftPriceCurrencyNumericCode;

        return $this;
    }

    /**
     * Get serviceLiftPriceCurrencyNumericCode
     *
     * @return integer 
     */
    public function getServiceLiftPriceCurrencyNumericCode()
    {
        return $this->serviceLiftPriceCurrencyNumericCode;
    }
}
