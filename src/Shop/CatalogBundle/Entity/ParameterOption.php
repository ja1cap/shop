<?php

namespace Shop\CatalogBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * ParameterOption
 */
class ParameterOption
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
    private $position;


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
     * @return ParameterOption
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
     * Set position
     *
     * @param integer $position
     * @return ParameterOption
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer 
     */
    public function getPosition()
    {
        return $this->position;
    }
    /**
     * @var integer
     */
    private $parameterId;

    /**
     * @var \Shop\CatalogBundle\Entity\Parameter
     */
    private $parameter;


    /**
     * Set parameterId
     *
     * @param integer $parameterId
     * @return ParameterOption
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
     * Set parameter
     *
     * @param \Shop\CatalogBundle\Entity\Parameter $parameter
     * @return ParameterOption
     */
    public function setParameter(Parameter $parameter = null)
    {
        $this->parameter = $parameter;
        $this->setPosition($parameter->getOptions()->count());
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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $optionValues;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->optionValues = new ArrayCollection();
    }

    /**
     * Add optionValues
     *
     * @param \Shop\CatalogBundle\Entity\ParameterValue $optionValues
     * @return ParameterOption
     */
    public function addOptionValue(ParameterValue $optionValues)
    {
        $this->optionValues[] = $optionValues;

        return $this;
    }

    /**
     * Remove optionValues
     *
     * @param \Shop\CatalogBundle\Entity\ParameterValue $optionValues
     */
    public function removeOptionValue(ParameterValue $optionValues)
    {
        $this->optionValues->removeElement($optionValues);
    }

    /**
     * Get optionValues
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOptionValues()
    {
        return $this->optionValues;
    }

    /**
     * @return string
     */
    function __toString()
    {
        return $this->getName();
    }

}
