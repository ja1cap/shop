<?php

namespace Shop\ShippingBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Weasty\Doctrine\Entity\AbstractEntity;

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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $categories;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $assemblyPrices;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $liftingPrices;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $prices;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $countries;

    /**
     * @var integer
     */
    private $status;

    /**
     * @var array
     */
    public static $statuses = array(
        self::STATUS_ON => 'Вкл',
        self::STATUS_OFF => 'Выкл',
    );

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->countries = new ArrayCollection();
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
     * @return mixed
     */
    public function getTextStatus(){
        return self::$statuses[$this->status];
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

    /**
     * Add assemblyPrices
     *
     * @param \Shop\ShippingBundle\Entity\ShippingMethodAssemblyPrice $assemblyPrices
     * @return ShippingMethod
     */
    public function addAssemblyPrice(ShippingMethodAssemblyPrice $assemblyPrices)
    {
        $this->assemblyPrices[] = $assemblyPrices;
        $assemblyPrices->setShippingMethod($this);
        return $this;
    }

    /**
     * Remove assemblyPrices
     *
     * @param \Shop\ShippingBundle\Entity\ShippingMethodAssemblyPrice $assemblyPrices
     */
    public function removeAssemblyPrice(ShippingMethodAssemblyPrice $assemblyPrices)
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
