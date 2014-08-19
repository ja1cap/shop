<?php

namespace Shop\CatalogBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Weasty\Doctrine\Entity\AbstractEntity;

/**
 * Class CategoryParameterGroup
 * @package Shop\CatalogBundle\Entity
 */
class CategoryParameterGroup extends AbstractEntity
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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $parameters;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->parameters = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return CategoryParameterGroup
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
     * @return CategoryParameterGroup
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
     * Add parameters
     *
     * @param \Shop\CatalogBundle\Entity\CategoryParameter $parameter
     * @return CategoryParameterGroup
     */
    public function addParameter(CategoryParameter $parameter)
    {
        $this->parameters[] = $parameter;
        $parameter->setGroup($this);
        return $this;
    }

    /**
     * Remove parameters
     *
     * @param \Shop\CatalogBundle\Entity\CategoryParameter $parameter
     */
    public function removeParameter(CategoryParameter $parameter)
    {
        $this->parameters->removeElement($parameter);
    }

    /**
     * Get parameters
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getParameters()
    {
        return $this->parameters;
    }
    /**
     * @var integer
     */
    private $categoryId;

    /**
     * @var \Shop\CatalogBundle\Entity\Category
     */
    private $category;


    /**
     * Set categoryId
     *
     * @param integer $categoryId
     * @return CategoryParameterGroup
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    /**
     * Get categoryId
     *
     * @return integer 
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * Set category
     *
     * @param \Shop\CatalogBundle\Entity\Category $category
     * @return CategoryParameterGroup
     */
    public function setCategory(Category $category = null)
    {
        $this->category = $category;
        $this->setCategoryId($category ? $category->getId() : null);
        $this->setPosition($category->getParameterGroups()->count());
        return $this;
    }

    /**
     * Get category
     *
     * @return \Shop\CatalogBundle\Entity\Category 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @return string
     */
    function __toString()
    {
        return $this->getName();
    }

}
