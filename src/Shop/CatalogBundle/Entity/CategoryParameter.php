<?php

namespace Shop\CatalogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CategoryParameter
 */
class CategoryParameter
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $categoryId;

    /**
     * @var integer
     */
    private $parameterId;

    /**
     * @var integer
     */
    private $position;

    /**
     * @var boolean
     */
    private $isMain;


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
     * @return CategoryParameter
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
     * Set parameterId
     *
     * @param integer $parameterId
     * @return CategoryParameter
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
     * Set position
     *
     * @param integer $position
     * @return CategoryParameter
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
     * Set isMain
     *
     * @param boolean $isMain
     * @return CategoryParameter
     */
    public function setIsMain($isMain)
    {
        $this->isMain = $isMain;

        return $this;
    }

    /**
     * Get isMain
     *
     * @return boolean 
     */
    public function getIsMain()
    {
        return $this->isMain;
    }
    /**
     * @var \Shop\CatalogBundle\Entity\Category
     */
    private $category;

    /**
     * @var \Shop\CatalogBundle\Entity\Parameter
     */
    private $parameter;


    /**
     * Set category
     *
     * @param \Shop\CatalogBundle\Entity\Category $category
     * @return CategoryParameter
     */
    public function setCategory(Category $category = null)
    {
        $this->category = $category;
        $this->setCategoryId($category->getId());
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
     * Set parameter
     *
     * @param \Shop\CatalogBundle\Entity\Parameter $parameter
     * @return CategoryParameter
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
}
