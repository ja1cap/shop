<?php
namespace Shop\CatalogBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Shop\CatalogBundle\Filter\FilterInterface;
use Weasty\DoctrineBundle\Entity\AbstractEntity;
use Weasty\CatalogBundle\Data\ProposalPriceInterface;

/**
 * Class Price
 * @package Shop\CatalogBundle\Entity
 */
class Price extends AbstractEntity
    implements ProposalPriceInterface
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
    private $sku;

    /**
     * @var string
     */
    private $manufacturerSku;

    /**
     * @var string
     */
    protected $value;

    /**
     * @return \Weasty\CatalogBundle\Data\CategoryInterface
     */
    public function getCategory()
    {
        return $this->getProposal()->getCategory();
    }

    /**
     * @return integer
     */
    public function getCategoryId()
    {
        return $this->getProposal()->getCategoryId();
    }

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
     * @TODO refactor
     * @return string
     */
    public function getDescription(){

        $parametersData = array();
        $category = $this->getCategory();
        $categoryParameters = array();

        if($category instanceof Category){
            foreach($category->getParameters() as $categoryParameter){
                if($categoryParameter instanceof CategoryParameter){
                    $categoryParameters[$categoryParameter->getParameterId()] = $categoryParameter;
                }
            }
        }

        /**
         * @var $parameterValue ParameterValue
         */
        foreach($this->getParameterValues() as $parameterValue){

            if(isset($categoryParameters[$parameterValue->getParameterId()])){

                $categoryParameter = $categoryParameters[$parameterValue->getParameterId()];
                if(
                    $categoryParameter instanceof CategoryParameter
                    && in_array($categoryParameter->getFilterGroup(), array(
                        FilterInterface::GROUP_MAIN,
                        FilterInterface::GROUP_EXTRA,
                    ))
                ){

                    if(!isset($parametersData[$parameterValue->getParameterId()])){

                        $parametersData[$parameterValue->getParameterId()] = array(
                            'parameterId' => $parameterValue->getParameterId(),
                            'parameterName' => $parameterValue->getParameter()->getName(),
                            'values' => array(),
                        );

                    }

                    $parametersData[$parameterValue->getParameterId()]['values'][] = $parameterValue->getOption()->getName();


                }

            }

        }

        return implode('', array_map(function($parameterData){

            return '<div>' . $parameterData['parameterName'] . ': ' . (implode(', ', $parameterData['values'])) . '</div>';

        }, $parametersData));

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
     * @return integer|string
     */
    public function getCurrency()
    {
        return $this->getCurrencyNumericCode();
    }

    /**
     * @param $currency
     * @return $this
     */
    public function setCurrency($currency){
        $this->currencyNumericCode = (int)$currency;
        return $this;
    }

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

    /**
     * Set manufacturerSku
     *
     * @param string $manufacturerSku
     * @return Price
     */
    public function setManufacturerSku($manufacturerSku)
    {
        $this->manufacturerSku = $manufacturerSku;

        return $this;
    }

    /**
     * Get manufacturerSku
     *
     * @return string 
     */
    public function getManufacturerSku()
    {
        return $this->manufacturerSku;
    }
}
