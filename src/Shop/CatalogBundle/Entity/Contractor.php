<?php

namespace Shop\CatalogBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Shop\CatalogBundle\Contractor\ContractorElement;
use Shop\CatalogBundle\Contractor\ContractorInterface;
use Weasty\Doctrine\Cache\Collection\CacheCollectionEntityInterface;
use Weasty\Doctrine\Entity\AbstractEntity;

/**
 * Class Contractor
 * @package Shop\CatalogBundle\Entity
 */
class Contractor extends AbstractEntity
    implements  ContractorInterface,
                CacheCollectionEntityInterface
{

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var integer
     */
    private $defaultCurrencyNumericCode;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $currencies;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->currencies = new ArrayCollection();
    }

    /**
     * @param $collection \Weasty\Doctrine\Cache\Collection\CacheCollection
     * @return \Weasty\Doctrine\Cache\Collection\CacheCollectionElementInterface
     */
    public function createCollectionElement($collection)
    {
        return new ContractorElement($collection, $this);
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
     * @return Contractor
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
     * Add currencies
     *
     * @param \Shop\CatalogBundle\Entity\ContractorCurrency $currency
     * @return Contractor
     */
    public function addCurrency(ContractorCurrency $currency)
    {
        $this->currencies[] = $currency;
        $currency->setContractor($this);
        return $this;
    }

    /**
     * Remove currencies
     *
     * @param \Shop\CatalogBundle\Entity\ContractorCurrency $currencies
     */
    public function removeCurrency(ContractorCurrency $currencies)
    {
        $this->currencies->removeElement($currencies);
    }

    /**
     * Get currencies
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCurrencies()
    {
        return $this->currencies;
    }

    /**
     * Set defaultCurrencyNumericCode
     *
     * @param integer $defaultCurrencyNumericCode
     * @return Contractor
     */
    public function setDefaultCurrencyNumericCode($defaultCurrencyNumericCode)
    {
        $this->defaultCurrencyNumericCode = $defaultCurrencyNumericCode;

        return $this;
    }

    /**
     * Get defaultCurrencyNumericCode
     *
     * @return integer 
     */
    public function getDefaultCurrencyNumericCode()
    {
        return $this->defaultCurrencyNumericCode;
    }

    /**
     * @return string
     */
    function __toString()
    {
        return $this->getName();
    }

}
