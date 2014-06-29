<?php

namespace Shop\CatalogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Weasty\Doctrine\Entity\AbstractEntity;

/**
 * Class ContractorCurrency
 * @package Shop\CatalogBundle\Entity
 */
class ContractorCurrency extends AbstractEntity
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
     * @var integer
     */
    private $numericCode;

    /**
     * @var float
     */
    private $value;


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
     * @var \Shop\CatalogBundle\Entity\Contractor
     */
    private $contractor;

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
