<?php

namespace Shop\CatalogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Shop\CatalogBundle\Parameter\Value\ParameterValueElement;
use Weasty\Bundle\CatalogBundle\Parameter\Value\ParameterValueInterface;
use Weasty\Doctrine\Cache\Collection\CacheCollectionEntityInterface;
use Weasty\Doctrine\Entity\AbstractEntity;

/**
 * Class ParameterValue
 * @package Shop\CatalogBundle\Entity
 */
class ParameterValue extends AbstractEntity
    implements  ParameterValueInterface,
                CacheCollectionEntityInterface
{

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $value;

    /**
     * @var integer
     */
    private $proposalId;

    /**
     * @var \Shop\CatalogBundle\Entity\Proposal
     */
    private $proposal;

    /**
     * @var integer
     */
    private $priceId;

    /**
     * @var \Shop\CatalogBundle\Entity\Price
     */
    private $price;

    /**
     * @var integer
     */
    private $parameterId;

    /**
     * @var \Shop\CatalogBundle\Entity\Parameter
     */
    private $parameter;

    /**
     * @var \Shop\CatalogBundle\Entity\ParameterOption
     */
    private $option;

    /**
     * @var integer
     */
    private $optionId;

    /**
     * @param $collection \Weasty\Doctrine\Cache\Collection\CacheCollection
     * @return \Weasty\Doctrine\Cache\Collection\CacheCollectionElementInterface
     */
    public function createCollectionElement($collection)
    {
        return new ParameterValueElement($collection, $this);
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
     * Set proposalId
     *
     * @param integer $proposalId
     * @return ParameterValue
     */
    protected function setProposalId($proposalId)
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
        $this
            ->setProposal($price ? $price->getProposal() : null)
            ->setPriceId($price ? $price->getId() : null)
        ;
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
    protected function setProposal(Proposal $proposal = null)
    {
        $this->proposal = $proposal;
        $this->setProposalId($proposal ? $proposal->getId() : null);
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
