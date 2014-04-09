<?php
namespace Shop\CatalogBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Weasty\DoctrineBundle\Entity\AbstractEntity;
use Weasty\ResourceBundle\Data\PriceInterface;

/**
 * Class Price
 * @package Shop\CatalogBundle\Entity
 */
class Price extends AbstractEntity
    implements PriceInterface
{

    const STATUS_ON = 1;
    const STATUS_OFF = 2;

    /**
     * @var array
     */
    public static $statuses = array(
        self::STATUS_ON => 'Вкл',
        self::STATUS_OFF => 'Выкл',
    );

    /**
     * @var string
     */
    protected $value;

    /**
     * @param string $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = preg_replace("/([^0-9\\.])/i", "", $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getExchangedValue(){

        $exchangedValue = $this->getValue();

        //@TODO refactor
        if($this->getCurrencyNumericCode() != ContractorCurrency::BLR_CURRENCY_NUMERIC_CODE){

            $priceCurrencyNumericCode = $this->getCurrencyNumericCode();
            if($this->getContractor()){

                $currency = $this->getContractor()->getCurrencies()->filter(function(ContractorCurrency $currency) use ($priceCurrencyNumericCode) {
                    return $currency->getNumericCode() == $priceCurrencyNumericCode;
                })->current();

                if($currency instanceof ContractorCurrency){
                    $exchangedValue = $this->getValue() * $currency->getValue();
                }

            }

        }

        return $exchangedValue;
    }

    /**
     * @var \Shop\CatalogBundle\Entity\Proposal
     */
    private $proposal;


    /**
     * Set proposal
     *
     * @param \Shop\CatalogBundle\Entity\Proposal $proposal
     * @return Price
     */
    public function setProposal(Proposal $proposal = null)
    {
        $this->proposal = $proposal;
        $this->proposalId = $proposal->getId();
        return $this;
    }

    /**
     * Get proposal
     *
     * @return \Shop\CatalogBundle\Entity\Proposal
     */
    public function getProposal()
    {
        return $this->proposal;
    }
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
    private $proposalId;

    /**
     * Set proposalId
     *
     * @param integer $proposalId
     * @return Price
     */
    public function setProposalId($proposalId)
    {
        $this->proposalId = $proposalId;

        return $this;
    }

    /**
     * Get proposalId
     *
     * @return integer 
     */
    public function getProposalId()
    {
        return $this->proposalId;
    }

    /**
     * @return string
     */
    function __toString()
    {
        return $this->getValue();
    }

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $parameterValues;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->parameterValues = new ArrayCollection();
    }

    /**
     * Add parameterValues
     *
     * @param \Shop\CatalogBundle\Entity\ParameterValue $parameterValues
     * @return Price
     */
    public function addParameterValue(ParameterValue $parameterValues)
    {
        $this->parameterValues[] = $parameterValues;
        $parameterValues->setPrice($this);
        return $this;
    }

    /**
     * Remove parameterValues
     *
     * @param \Shop\CatalogBundle\Entity\ParameterValue $parameterValues
     */
    public function removeParameterValue(ParameterValue $parameterValues)
    {
        $this->parameterValues->removeElement($parameterValues);
    }

    /**
     * Get parameterValues
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getParameterValues()
    {
        return $this->parameterValues;
    }

    /**
     * @return string
     */
    public function getDescription(){

        return implode('', $this->getParameterValues()->map(function(ParameterValue $parameterValue){

            return '<div>' . $parameterValue->getParameter()->getName() . ': ' . $parameterValue->getOption()->getName() . '</div>';

        })->toArray());

    }

    /**
     * @var integer
     */
    private $contractorId;

    /**
     * @var \Shop\CatalogBundle\Entity\Contractor
     */
    private $contractor;


    /**
     * Set contractorId
     *
     * @param integer $contractorId
     * @return Price
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
     * Set contractor
     *
     * @param \Shop\CatalogBundle\Entity\Contractor $contractor
     * @return Price
     */
    public function setContractor(Contractor $contractor = null)
    {
        $this->contractor = $contractor;
        $this->contractorId = $contractor ? $contractor->getId() : null;
        return $this;
    }

    /**
     * Get contractor
     *
     * @return \Shop\CatalogBundle\Entity\Contractor 
     */
    public function getContractor()
    {
        return $this->contractor ?: ($this->getProposal() ? $this->getProposal()->getDefaultContractor() : null);
    }

    /**
     * @return null|string
     */
    public function getContractorName(){
        return $this->getContractor() ? $this->getContractor()->getName() : null;
    }

    /**
     * @var integer
     */
    private $currencyNumericCode;


    /**
     * Set currencyNumericCode
     *
     * @param integer $currencyNumericCode
     * @return Price
     */
    public function setCurrencyNumericCode($currencyNumericCode)
    {
        $this->currencyNumericCode = $currencyNumericCode;

        return $this;
    }

    /**
     * Get currencyNumericCode
     *
     * @return integer 
     */
    public function getCurrencyNumericCode()
    {
        return $this->currencyNumericCode;
    }

    /**
     * Get currencyAlphabeticCode
     *
     * @return integer
     */
    public function getCurrencyAlphabeticCode()
    {
        if(!isset(ContractorCurrency::$currenciesNumericCodesAlphabeticCodes[$this->getCurrencyNumericCode()])){
            return false;
        }
        return ContractorCurrency::$currenciesNumericCodesAlphabeticCodes[$this->getCurrencyNumericCode()];
    }

    /**
     * @return bool
     */
    public function getCurrencyName(){
        if(!isset(ContractorCurrency::$currencyNames[$this->getCurrencyNumericCode()])){
            return false;
        }
        return ContractorCurrency::$currencyNames[$this->getCurrencyNumericCode()];
    }

    /**
     * @return bool
     */
    public function getCurrencyShortName(){
        if(!isset(ContractorCurrency::$currencyShortNames[$this->getCurrencyNumericCode()])){
            return false;
        }
        return ContractorCurrency::$currencyShortNames[$this->getCurrencyNumericCode()];
    }

    /**
     * @var string
     */
    private $sku;


    /**
     * Set sku
     *
     * @param string $sku
     * @return Price
     */
    public function setSku($sku)
    {
        $this->sku = $sku;

        return $this;
    }

    /**
     * Get sku
     *
     * @return string 
     */
    public function getSku()
    {
        return $this->sku;
    }
    /**
     * @var integer
     */
    private $status;


    /**
     * Set status
     *
     * @param integer $status
     * @return Price
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
     * @var integer
     */
    private $warehouseAmount;

    /**
     * @var \DateTime
     */
    private $warehouseAmountUpdateDate;


    /**
     * Set warehouseAmount
     *
     * @param integer $warehouseAmount
     * @return Price
     */
    public function setWarehouseAmount($warehouseAmount)
    {
        $this->warehouseAmount = $warehouseAmount;

        return $this;
    }

    /**
     * Get warehouseAmount
     *
     * @return integer 
     */
    public function getWarehouseAmount()
    {
        return $this->warehouseAmount;
    }

    /**
     * Set warehouseAmountUpdateDate
     *
     * @param \DateTime $warehouseAmountUpdateDate
     * @return Price
     */
    public function setWarehouseAmountUpdateDate($warehouseAmountUpdateDate)
    {
        $this->warehouseAmountUpdateDate = $warehouseAmountUpdateDate;

        return $this;
    }

    /**
     * Get warehouseAmountUpdateDate
     *
     * @return \DateTime 
     */
    public function getWarehouseAmountUpdateDate()
    {
        return $this->warehouseAmountUpdateDate;
    }
}
