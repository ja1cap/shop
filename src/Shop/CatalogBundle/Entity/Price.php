<?php
namespace Shop\CatalogBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Shop\MainBundle\Entity\AbstractEntity;

/**
 * Class Price
 * @package Shop\CatalogBundle\Entity
 */
class Price extends AbstractEntity {

    /**
     * @var string
     */
    protected $value;

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = preg_replace("/([^0-9\\.])/i", "", $value);
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
     * @return string
     */
    public function getDescription(){

        return implode("\n", $this->getParameterValues()->map(function(ParameterValue $parameterValue){

            return $parameterValue->getParameter()->getName() . ': ' . $parameterValue->getOption()->getName();

        })->toArray());

    }

}
