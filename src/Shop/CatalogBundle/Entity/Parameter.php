<?php

namespace Shop\CatalogBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Shop\MainBundle\Entity\AbstractEntity;

/**
 * Parameter
 */
class Parameter extends AbstractEntity
{

    const TYPE_SELECT = 1;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var boolean
     */
    private $isPriceParameter;

    /**
     * @var integer
     */
    private $type;


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
     * @return Parameter
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
     * Set isPriceParameter
     *
     * @param boolean $isPriceParameter
     * @return Parameter
     */
    public function setIsPriceParameter($isPriceParameter)
    {
        $this->isPriceParameter = $isPriceParameter;

        return $this;
    }

    /**
     * Get isPriceParameter
     *
     * @return boolean 
     */
    public function getIsPriceParameter()
    {
        return $this->isPriceParameter;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return Parameter
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $options;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->options = new ArrayCollection();
        $this->categoryParameters = new ArrayCollection();
    }

    /**
     * Add options
     *
     * @param \Shop\CatalogBundle\Entity\ParameterOption $option
     * @return Parameter
     */
    public function addOption(ParameterOption $option)
    {
        $this->options[] = $option;
        $option->setParameter($this);
        return $this;
    }

    /**
     * Remove options
     *
     * @param \Shop\CatalogBundle\Entity\ParameterOption $options
     */
    public function removeOption(ParameterOption $options)
    {
        $this->options->removeElement($options);
    }

    /**
     * Get options
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOptions()
    {
        return $this->options;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $categoryParameters;


    /**
     * Add categoryParameters
     *
     * @param \Shop\CatalogBundle\Entity\CategoryParameter $categoryParameters
     * @return Parameter
     */
    public function addCategoryParameter(CategoryParameter $categoryParameters)
    {
        $this->categoryParameters[] = $categoryParameters;
        $categoryParameters->setParameter($this);
        return $this;
    }

    /**
     * Remove categoryParameters
     *
     * @param \Shop\CatalogBundle\Entity\CategoryParameter $categoryParameters
     */
    public function removeCategoryParameter(CategoryParameter $categoryParameters)
    {
        $this->categoryParameters->removeElement($categoryParameters);
    }

    /**
     * Get categoryParameters
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCategoryParameters()
    {
        return $this->categoryParameters;
    }
}
