<?php

namespace Shop\CatalogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Weasty\DoctrineBundle\Entity\AbstractEntity;

/**
 * Class CategoryFilters
 * @package Shop\CatalogBundle\Entity
 */
class CategoryFilters extends AbstractEntity
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
    private $categoryId;

    /**
     * @var string
     */
    private $slug;

    /**
     * @var array
     */
    private $parameterFilters;

    /**
     * @var array
     */
    private $manufacturerFilter;

    /**
     * @var \Shop\CatalogBundle\Entity\Category
     */
    private $category;

    function __construct($manufacturerFilter = array(), $parameterFilters = array(), $priceRangeFilter = array())
    {
        $this->manufacturerFilter = $manufacturerFilter;
        $this->parameterFilters = $parameterFilters;
        $this->priceRangeFilter = $priceRangeFilter;
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
     * Set categoryId
     *
     * @param integer $categoryId
     * @return CategoryFilters
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
     * Set slug
     *
     * @param string $slug
     * @return CategoryFilters
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set filters
     *
     * @param array $filters
     * @return CategoryFilters
     */
    public function setParameterFilters($filters)
    {
        $this->parameterFilters = array_filter($filters);
        return $this;
    }

    /**
     * Get filters
     *
     * @return array 
     */
    public function getParameterFilters()
    {
        return $this->parameterFilters;
    }

    /**
     * Set category
     *
     * @param \Shop\CatalogBundle\Entity\Category $category
     * @return CategoryFilters
     */
    public function setCategory(Category $category = null)
    {
        $this->category = $category;
        $this->categoryId = $category ? $category->getId() : null;
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
     * Set manufacturerFilter
     *
     * @param array $manufacturerFilter
     * @return CategoryFilters
     */
    public function setManufacturerFilter($manufacturerFilter)
    {
        $this->manufacturerFilter = $manufacturerFilter;

        return $this;
    }

    /**
     * Get manufacturerFilter
     *
     * @return array 
     */
    public function getManufacturerFilter()
    {
        return $this->manufacturerFilter;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return CategoryFilters
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
     * @var array
     */
    private $priceRangeFilter;


    /**
     * Set priceRangeFilter
     *
     * @param array $priceRangeFilter
     * @return CategoryFilters
     */
    public function setPriceRangeFilter($priceRangeFilter)
    {
        $this->priceRangeFilter = $priceRangeFilter;

        return $this;
    }

    /**
     * Get priceRangeFilter
     *
     * @return array 
     */
    public function getPriceRangeFilter()
    {
        return $this->priceRangeFilter;
    }
}
