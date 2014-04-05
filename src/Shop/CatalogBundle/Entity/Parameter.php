<?php

namespace Shop\CatalogBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Weasty\DoctrineBundle\Entity\AbstractEntity;

/**
 * Parameter
 */
class Parameter extends \Weasty\DoctrineBundle\Entity\AbstractEntity
{

    const TYPE_SELECT = 1;
    const TYPE_CHECKBOXES = 2;

    /**
     * @var array
     */
    public static $types = array(
        self::TYPE_SELECT => 'Выпадающий список',
        self::TYPE_CHECKBOXES => 'Флажки',
    );

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

    /**
     * @return string
     */
    function __toString()
    {
        return $this->getName();
    }

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $values;


    /**
     * Add values
     *
     * @param \Shop\CatalogBundle\Entity\ParameterValue $values
     * @return Parameter
     */
    public function addValue(ParameterValue $values)
    {
        $this->values[] = $values;
        $values->setParameter($this);
        return $this;
    }

    /**
     * Remove values
     *
     * @param \Shop\CatalogBundle\Entity\ParameterValue $values
     */
    public function removeValue(ParameterValue $values)
    {
        $this->values->removeElement($values);
    }

    /**
     * Get values
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getValues()
    {
        return $this->values;
    }
    /**
     * @var integer
     */
    private $defaultOptionId;

    /**
     * @var \Shop\CatalogBundle\Entity\ParameterOption
     */
    private $defaultOption;

    /**
     * Set defaultOptionId
     *
     * @param integer $defaultOptionId
     * @return Parameter
     */
    public function setDefaultOptionId($defaultOptionId)
    {
        $this->defaultOptionId = $defaultOptionId;

        return $this;
    }

    /**
     * Get defaultOptionId
     *
     * @return integer 
     */
    public function getDefaultOptionId()
    {
        return $this->defaultOptionId;
    }

    /**
     * Set defaultOption
     *
     * @param \Shop\CatalogBundle\Entity\ParameterOption $defaultOption
     * @return Parameter
     */
    public function setDefaultOption(ParameterOption $defaultOption = null)
    {
        $this->defaultOption = $defaultOption;
        $this->defaultOptionId = $defaultOption ? $defaultOption->getId() : null;
        return $this;
    }

    /**
     * Get defaultOption
     *
     * @return \Shop\CatalogBundle\Entity\ParameterOption 
     */
    public function getDefaultOption()
    {
        return $this->defaultOption;
    }
}
