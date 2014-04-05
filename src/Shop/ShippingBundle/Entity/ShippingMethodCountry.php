<?php

namespace Shop\ShippingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Weasty\DoctrineBundle\Entity\AbstractEntity;

/**
 * Class ShippingMethodCountry
 * @package Shop\ShippingBundle\Entity
 */
class ShippingMethodCountry extends AbstractEntity
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $countryCode;

    /**
     * @var array
     */
    private $cityGeonameIds;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set countryCode
     *
     * @param string $countryCode
     * @return ShippingMethodCountry
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    /**
     * Get countryCode
     *
     * @return string 
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * Set cityGeonameIds
     *
     * @param array $cityGeonameIds
     * @return ShippingMethodCountry
     */
    public function setCityGeonameIds($cityGeonameIds)
    {
        $this->cityGeonameIds = $cityGeonameIds;

        return $this;
    }

    /**
     * Get cityGeonameIds
     *
     * @return array 
     */
    public function getCityGeonameIds()
    {
        return $this->cityGeonameIds;
    }

    /**
     * @var integer
     */
    private $shippingMethodId;

    /**
     * @var \Shop\ShippingBundle\Entity\ShippingMethod
     */
    private $shippingMethod;


    /**
     * Set shippingMethodId
     *
     * @param integer $shippingMethodId
     * @return ShippingMethodCountry
     */
    public function setShippingMethodId($shippingMethodId)
    {
        $this->shippingMethodId = $shippingMethodId;

        return $this;
    }

    /**
     * Get shippingMethodId
     *
     * @return integer 
     */
    public function getShippingMethodId()
    {
        return $this->shippingMethodId;
    }

    /**
     * Set shippingMethod
     *
     * @param \Shop\ShippingBundle\Entity\ShippingMethod $shippingMethod
     * @return ShippingMethodCountry
     */
    public function setShippingMethod(ShippingMethod $shippingMethod = null)
    {
        $this->shippingMethod = $shippingMethod;
        $this->shippingMethodId = $shippingMethod->getId();
        return $this;
    }

    /**
     * Get shippingMethod
     *
     * @return \Shop\ShippingBundle\Entity\ShippingMethod
     */
    public function getShippingMethod()
    {
        return $this->shippingMethod;
    }

}
