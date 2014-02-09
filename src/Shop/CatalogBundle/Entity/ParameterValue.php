<?php

namespace Shop\CatalogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ParameterValue
 */
class ParameterValue
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $parameterId;

    /**
     * @var string
     */
    private $value;

    /**
     * @var integer
     */
    private $optionId;


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
     * Set parameterId
     *
     * @param integer $parameterId
     * @return ParameterValue
     */
    public function setParameterId($parameterId)
    {
        $this->parameterId = $parameterId;

        return $this;
    }

    /**
     * Get parameterId
     *
     * @return integer 
     */
    public function getParameterId()
    {
        return $this->parameterId;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return ParameterValue
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set optionId
     *
     * @param integer $optionId
     * @return ParameterValue
     */
    public function setOptionId($optionId)
    {
        $this->optionId = $optionId;

        return $this;
    }

    /**
     * Get optionId
     *
     * @return integer 
     */
    public function getOptionId()
    {
        return $this->optionId;
    }
    /**
     * @var integer
     */
    private $proposalId;

    /**
     * @var \Shop\CatalogBundle\Entity\Price
     */
    private $price;

    /**
     * @var \Shop\CatalogBundle\Entity\Proposal
     */
    private $proposal;

    /**
     * @var \Shop\CatalogBundle\Entity\Parameter
     */
    private $parameter;

    /**
     * @var \Shop\CatalogBundle\Entity\ParameterOption
     */
    private $option;


    /**
     * Set proposalId
     *
     * @param integer $proposalId
     * @return ParameterValue
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
     * Set price
     *
     * @param \Shop\CatalogBundle\Entity\Price $price
     * @return ParameterValue
     */
    public function setPrice(Price $price = null)
    {
        $this->price = $price;
        $this->setPriceId($price->getId());
        return $this;
    }

    /**
     * Get price
     *
     * @return \Shop\CatalogBundle\Entity\Price 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set proposal
     *
     * @param \Shop\CatalogBundle\Entity\Proposal $proposal
     * @return ParameterValue
     */
    public function setProposal(Proposal $proposal = null)
    {
        $this->proposal = $proposal;
        $this->setProposalId($proposal->getId());
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
     * Set parameter
     *
     * @param \Shop\CatalogBundle\Entity\Parameter $parameter
     * @return ParameterValue
     */
    public function setParameter(Parameter $parameter = null)
    {
        $this->parameter = $parameter;
        $this->setParameterId($parameter->getId());
        return $this;
    }

    /**
     * Get parameter
     *
     * @return \Shop\CatalogBundle\Entity\Parameter 
     */
    public function getParameter()
    {
        return $this->parameter;
    }

    /**
     * Set option
     *
     * @param \Shop\CatalogBundle\Entity\ParameterOption $option
     * @return ParameterValue
     */
    public function setOption(ParameterOption $option = null)
    {
        $this->option = $option;
        $this->setOptionId($option->getId());
        return $this;
    }

    /**
     * Get option
     *
     * @return \Shop\CatalogBundle\Entity\ParameterOption 
     */
    public function getOption()
    {
        return $this->option;
    }
    /**
     * @var integer
     */
    private $priceId;


    /**
     * Set priceId
     *
     * @param integer $priceId
     * @return ParameterValue
     */
    public function setPriceId($priceId)
    {
        $this->priceId = $priceId;

        return $this;
    }

    /**
     * Get priceId
     *
     * @return integer 
     */
    public function getPriceId()
    {
        return $this->priceId;
    }
}
