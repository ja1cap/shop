<?php

namespace Shop\ShippingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class ShippingMethodCategoryAssemblyPrice
 * @package Shop\ShippingBundle\Entity
 */
class ShippingMethodCategoryAssemblyPrice extends ShippingAssemblyPrice
{
    /**
     * @var integer
     */
    private $shippingCategoryId;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Shop\ShippingBundle\Entity\ShippingMethodCategory
     */
    private $shippingCategory;


    /**
     * Set shippingCategoryId
     *
     * @param integer $shippingCategoryId
     * @return ShippingMethodCategoryAssemblyPrice
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set shippingCategory
     *
     * @param \Shop\ShippingBundle\Entity\ShippingMethodCategory $shippingCategory
     * @return ShippingMethodCategoryAssemblyPrice
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
