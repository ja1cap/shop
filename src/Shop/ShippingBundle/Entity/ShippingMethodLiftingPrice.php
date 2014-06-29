<?php

namespace Shop\ShippingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class ShippingMethodLiftingPrice
 * @package Shop\ShippingBundle\Entity
 */
class ShippingMethodLiftingPrice extends ShippingLiftingPrice
{
    /**
     * @var integer
     */
    private $shippingMethodId;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Shop\ShippingBundle\Entity\ShippingMethod
     */
    private $shippingMethod;


    /**
     * Set shippingMethodId
     *
     * @param integer $shippingMethodId
     * @return ShippingMethodLiftingPrice
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set shippingMethod
     *
     * @param \Shop\ShippingBundle\Entity\ShippingMethod $shippingMethod
     * @return ShippingMethodLiftingPrice
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
