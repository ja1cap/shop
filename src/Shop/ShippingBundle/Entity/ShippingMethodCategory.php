<?php

namespace Shop\ShippingBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Weasty\DoctrineBundle\Entity\AbstractEntity;

/**
 * Class ShippingMethodCategory
 * @package Shop\ShippingBundle\Entity
 */
class ShippingMethodCategory extends AbstractEntity
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
     * @var array
     */
    private $categoryIds;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $prices;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $liftingPrices;

    function __construct()
    {
        $this->prices = new ArrayCollection();
        $this->liftingPrices = new ArrayCollection();
        $this->assemblyPrices = new ArrayCollection();
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
     * Set shippingMethodId
     *
     * @param integer $shippingMethodId
     * @return ShippingMethodCategory
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
     * @return ShippingMethodCategory
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


    /**
     * Set categoryIds
     *
     * @param array $categoryIds
     * @return ShippingMethodCategory
     */
    public function setCategoryIds($categoryIds)
    {
        $this->categoryIds = $categoryIds;

        return $this;
    }

    /**
     * Get categoryIds
     *
     * @return array 
     */
    public function getCategoryIds()
    {
        return $this->categoryIds;
    }

    /**
     * Add prices
     *
     * @param \Shop\ShippingBundle\Entity\ShippingMethodCategoryPrice $price
     * @return ShippingMethodCategory
     */
    public function addPrice(ShippingMethodCategoryPrice $price)
    {
        $this->prices[] = $price;
        $price->setShippingCategory($this);
        return $this;
    }

    /**
     * Remove prices
     *
     * @param \Shop\ShippingBundle\Entity\ShippingMethodCategoryPrice $prices
     */
    public function removePrice(ShippingMethodCategoryPrice $prices)
    {
        $this->prices->removeElement($prices);
    }

    /**
     * Get prices
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPrices()
    {
        return $this->prices;
    }

    /**
     * Add liftingPrices
     *
     * @param \Shop\ShippingBundle\Entity\ShippingMethodCategoryLiftingPrice $liftingPrice
     * @return ShippingMethodCategory
     */
    public function addLiftingPrice(ShippingMethodCategoryLiftingPrice $liftingPrice)
    {
        $this->liftingPrices[] = $liftingPrice;
        $liftingPrice->setShippingCategory($this);
        return $this;
    }

    /**
     * Remove liftingPrices
     *
     * @param \Shop\ShippingBundle\Entity\ShippingMethodCategoryLiftingPrice $liftingPrice
     */
    public function removeLiftingPrice(ShippingMethodCategoryLiftingPrice $liftingPrice)
    {
        $this->liftingPrices->removeElement($liftingPrice);
    }

    /**
     * Get liftingPrices
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLiftingPrices()
    {
        return $this->liftingPrices;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $assemblyPrices;


    /**
     * Add assemblyPrices
     *
     * @param \Shop\ShippingBundle\Entity\ShippingMethodCategoryAssemblyPrice $assemblyPrices
     * @return ShippingMethodCategory
     */
    public function addAssemblyPrice(ShippingMethodCategoryAssemblyPrice $assemblyPrices)
    {
        $this->assemblyPrices[] = $assemblyPrices;
        $assemblyPrices->setShippingCategory($this);
        return $this;
    }

    /**
     * Remove assemblyPrices
     *
     * @param \Shop\ShippingBundle\Entity\ShippingMethodCategoryAssemblyPrice $assemblyPrices
     */
    public function removeAssemblyPrice(ShippingMethodCategoryAssemblyPrice $assemblyPrices)
    {
        $this->assemblyPrices->removeElement($assemblyPrices);
    }

    /**
     * Get assemblyPrices
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAssemblyPrices()
    {
        return $this->assemblyPrices;
    }
}
