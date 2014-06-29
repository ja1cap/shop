<?php

namespace Shop\ShippingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class ShippingMethodCategoryPrice
 * @package Shop\ShippingBundle\Entity
 */
class ShippingMethodCategoryPrice extends ShippingPrice
{

    /**
     * @var integer
     */
    private $id;

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
     * @var integer
     */
    private $shippingCategoryId;

    /**
     * @var \Shop\ShippingBundle\Entity\ShippingMethodCategory
     */
    private $shippingCategory;


    /**
     * Set shippingCategoryId
     *
     * @param integer $shippingCategoryId
     * @return ShippingMethodCategoryPrice
     */
    public function setShippingCategoryId($shippingCategoryId)
    {
        $this->shippingCategoryId = $shippingCategoryId;

        return $this;
    }

    /**
     * Get shippingCategoryId
     *
     * @return integer 
     */
    public function getShippingCategoryId()
    {
        return $this->shippingCategoryId;
    }

    /**
     * Set shippingCategory
     *
     * @param \Shop\ShippingBundle\Entity\ShippingMethodCategory $shippingCategory
     * @return ShippingMethodCategoryPrice
     */
    public function setShippingCategory(ShippingMethodCategory $shippingCategory = null)
    {
        $this->shippingCategory = $shippingCategory;
        $this->shippingCategoryId = $shippingCategory ? $shippingCategory->getId() : null;
        return $this;
    }

    /**
     * Get shippingCategory
     *
     * @return \Shop\ShippingBundle\Entity\ShippingMethodCategory 
     */
    public function getShippingCategory()
    {
        return $this->shippingCategory;
    }
}
