<?php

namespace Shop\ShippingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Weasty\DoctrineBundle\Entity\AbstractEntity;
use Weasty\MoneyBundle\Data\PriceInterface;

/**
 * Class ShippingLiftingPrice
 * @package Shop\ShippingBundle\Entity
 */
abstract class ShippingLiftingPrice extends AbstractEntity
    implements PriceInterface
{

    const FLOOR_AMOUNT_TYPE_ANY = 1;
    const FLOOR_AMOUNT_TYPE_RANGE = 2;

    const LIFTING_TYPE_COMMON = 1;
    const LIFTING_TYPE_LIFT = 2;
    const LIFTING_TYPE_SERVICE_LIFT = 3;

    /**
     * @var integer
     */
    protected $floorAmountType;

    /**
     * @var integer
     */
    private $minFloor;

    /**
     * @var integer
     */
    private $maxFloor;

    /**
     * @var integer
     */
    private $value;

    /**
     * @var integer
     */
    private $currencyNumericCode;

    /**
     * @var integer
     */
    private $liftingType;

    function __construct()
    {
        $this->floorAmountType = self::FLOOR_AMOUNT_TYPE_ANY;
        $this->liftingType = self::LIFTING_TYPE_COMMON;
    }

    /**
     * Set floorAmountType
     *
     * @param integer $floorAmountType
     * @return ShippingLiftingPrice
     */
    public function setFloorAmountType($floorAmountType)
    {
        $this->floorAmountType = $floorAmountType;

        return $this;
    }

    /**
     * Get floorAmountType
     *
     * @return integer 
     */
    public function getFloorAmountType()
    {
        return $this->floorAmountType;
    }

    /**
     * Set minFloor
     *
     * @param integer $minFloor
     * @return ShippingLiftingPrice
     */
    public function setMinFloor($minFloor)
    {
        $this->minFloor = $minFloor;

        return $this;
    }

    /**
     * Get minFloor
     *
     * @return integer 
     */
    public function getMinFloor()
    {
        return $this->minFloor;
    }

    /**
     * Set maxFloor
     *
     * @param integer $maxFloor
     * @return ShippingLiftingPrice
     */
    public function setMaxFloor($maxFloor)
    {
        $this->maxFloor = $maxFloor;

        return $this;
    }

    /**
     * Get maxFloor
     *
     * @return integer 
     */
    public function getMaxFloor()
    {
        return $this->maxFloor;
    }

    /**
     * Set value
     *
     * @param integer $value
     * @return ShippingLiftingPrice
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
     * @return ShippingLiftingPrice
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
     * Set liftingType
     *
     * @param integer $liftingType
     * @return ShippingLiftingPrice
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
        return $this->liftingType;
    }
}
