<?php

namespace Shop\ShippingBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Weasty\DoctrineBundle\Entity\AbstractEntity;

/**
 * Class ShippingMethod
 * @package Shop\ShippingBundle\Entity
 */
class ShippingMethod extends AbstractEntity
{

    const STATUS_ON = 1;
    const STATUS_OFF = 2;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $description;

    /**
     * @var array
     */
    public static $statuses = array(
        self::STATUS_ON => 'Вкл',
        self::STATUS_OFF => 'Выкл',
    );

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
     * Set name
     *
     * @param string $name
     * @return ShippingMethod
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return ShippingMethod
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }
    /**
     * @var integer
     */
    private $status;


    /**
     * Set status
     *
     * @param integer $status
     * @return ShippingMethod
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $countries;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->countries = new ArrayCollection();
        $this->prices = new ArrayCollection();
        $this->liftingPrices = new ArrayCollection();
    }

    /**
     * Add countries
     *
     * @param \Shop\ShippingBundle\Entity\ShippingMethodCountry $country
     * @return ShippingMethod
     */
    public function addCountry(ShippingMethodCountry $country)
    {
        $this->countries[] = $country;
        $country->setShippingMethod($this);
        return $this;
    }

    /**
     * Remove countries
     *
     * @param \Shop\ShippingBundle\Entity\ShippingMethodCountry $countries
     */
    public function removeCountry(ShippingMethodCountry $countries)
    {
        $this->countries->removeElement($countries);
    }

    /**
     * Get countries
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCountries()
    {
        if(!$this->countries){
            $this->countries = new ArrayCollection();
        }
        return $this->countries;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $prices;


    /**
     * Add prices
     *
     * @param \Shop\ShippingBundle\Entity\ShippingMethodPrice $price
     * @return ShippingMethod
     */
    public function addPrice(ShippingMethodPrice $price)
    {
        $this->prices[] = $price;
        $price->setShippingMethod($this);
        return $this;
    }

    /**
     * Remove prices
     *
     * @param \Shop\ShippingBundle\Entity\ShippingMethodPrice $prices
     */
    public function removePrice(ShippingMethodPrice $prices)
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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $liftingPrices;


    /**
     * Add liftingPrices
     *
     * @param \Shop\ShippingBundle\Entity\ShippingMethodLiftingPrice $liftingPrices
     * @return ShippingMethod
     */
    public function addLiftingPrice(ShippingMethodLiftingPrice $liftingPrices)
    {
        $this->liftingPrices[] = $liftingPrices;
        $liftingPrices->setShippingMethod($this);
        return $this;
    }

    /**
     * Remove liftingPrices
     *
     * @param \Shop\ShippingBundle\Entity\ShippingMethodLiftingPrice $liftingPrices
     */
    public function removeLiftingPrice(ShippingMethodLiftingPrice $liftingPrices)
    {
        $this->liftingPrices->removeElement($liftingPrices);
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
    private $categories;


    /**
     * Add categories
     *
     * @param \Shop\ShippingBundle\Entity\ShippingMethodCategory $category
     * @return ShippingMethod
     */
    public function addCategory(ShippingMethodCategory $category)
    {
        $this->categories[] = $category;
        $category->setShippingMethod($this);
        return $this;
    }

    /**
     * Remove categories
     *
     * @param \Shop\ShippingBundle\Entity\ShippingMethodCategory $categories
     */
    public function removeCategory(ShippingMethodCategory $categories)
    {
        $this->categories->removeElement($categories);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCategories()
    {
        return $this->categories;
    }
}
