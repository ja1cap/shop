<?php

namespace Shop\CatalogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Shop\CatalogBundle\Contractor\Currency\ContractorCurrencyElement;
use Shop\CatalogBundle\Contractor\Currency\ContractorCurrencyInterface;
use Weasty\Doctrine\Cache\Collection\CacheCollectionEntityInterface;
use Weasty\Doctrine\Entity\AbstractEntity;

/**
 * Class ContractorCurrency
 * @package Shop\CatalogBundle\Entity
 */
class ContractorCurrency extends AbstractEntity
    implements  ContractorCurrencyInterface,
                CacheCollectionEntityInterface
{

    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $contractorId;

    /**
     * @var \Shop\CatalogBundle\Entity\Contractor
     */
    private $contractor;

    /**
     * @var integer
     */
    private $numericCode;

    /**
     * @var float
     */
    private $value;

    /**
     * @param $collection \Weasty\Doctrine\Cache\Collection\CacheCollection
     * @return \Weasty\Doctrine\Cache\Collection\CacheCollectionElementInterface
     */
    public function createCollectionElement($collection)
    {
        return new ContractorCurrencyElement($collection, $this);
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
     * Set contractorId
     *
     * @param integer $contractorId
     * @return ContractorCurrency
     */
    public function setContractorId($contractorId)
    {
        $this->contractorId = $contractorId;

        return $this;
    }

    /**
     * Get contractorId
     *
     * @return integer 
     */
    public function getContractorId()
    {
        return $this->contractorId;
    }

    /**
     * Set numericCode
     *
     * @param integer $numericCode
     * @return ContractorCurrency
     */
    public function setNumericCode($numericCode)
    {
        $this->numericCode = $numericCode;

        return $this;
    }

    /**
     * Get numericCode
     *
     * @return integer 
     */
    public function getNumericCode()
    {
        return $this->numericCode;
    }

    /**
     * Set value
     *
     * @param float $value
     * @return ContractorCurrency
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return float 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set contractor
     *
     * @param \Shop\CatalogBundle\Entity\Contractor $contractor
     * @return ContractorCurrency
     */
    public function setContractor(Contractor $contractor = null)
    {
        $this->contractor = $contractor;
        $this->contractorId = $contractor->getId();
        return $this;
    }

    /**
     * Get contractor
     *
     * @return \Shop\CatalogBundle\Entity\Contractor 
     */
    public function getContractor()
    {
        return $this->contractor;
    }
}
