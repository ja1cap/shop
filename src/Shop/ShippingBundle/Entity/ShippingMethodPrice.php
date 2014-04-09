<?php

namespace Shop\ShippingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class ShippingMethodPrice
 * @package Shop\ShippingBundle\Entity
 */
class ShippingMethodPrice extends ShippingPrice
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $shippingMethodId;

    /**
     * @var \Shop\ShippingBundle\Entity\ShippingMethod
     */
    private $shippingMethod;

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
     * Set shippingMethodId
     *
     * @param integer $shippingMethodId
     * @return ShippingMethodPrice
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
     * @return ShippingMethodPrice
     */
    public function setShippingMethod(ShippingMethod $shippingMethod = null)
    {
        $this->shippingMethod = $shippingMethod;
        $this->shippingMethodId = $shippingMethod ? $shippingMethod->getId() : null;
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
