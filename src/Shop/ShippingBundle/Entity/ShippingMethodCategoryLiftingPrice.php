<?php

namespace Shop\ShippingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class ShippingMethodCategoryLiftingPrice
 * @package Shop\ShippingBundle\Entity
 */
class ShippingMethodCategoryLiftingPrice extends ShippingLiftingPrice
{

    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $shippingCategoryId;

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
     * Set shippingCategoryId
     *
     * @param integer $shippingCategoryId
     * @return ShippingMethodCategoryLiftingPrice
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
     * @var \Shop\ShippingBundle\Entity\ShippingMethodCategory
     */
    private $shippingCategory;


    /**
     * Set shippingCategory
     *
     * @param \Shop\ShippingBundle\Entity\ShippingMethodCategory $shippingCategory
     * @return ShippingMethodCategoryLiftingPrice
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
