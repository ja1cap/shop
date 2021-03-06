<?php

namespace Shop\CatalogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Shop\CatalogBundle\Category\Parameter\CategoryParameterElement;
use Shop\CatalogBundle\Category\Parameter\CategoryParameterInterface;
use Weasty\Doctrine\Cache\Collection\CacheCollectionEntityInterface;
use Weasty\Doctrine\Entity\AbstractEntity;

/**
 * Class CategoryParameter
 * @package Shop\CatalogBundle\Entity
 */
class CategoryParameter extends AbstractEntity
    implements  CategoryParameterInterface,
                CacheCollectionEntityInterface
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
     * @var bool
     */
    private $isComparable;

    /**
     * @var integer
     */
    private $position;

    /**
     * @var array
     */
    private $filterGroups = array();

    /**
     * @var \Shop\CatalogBundle\Entity\Category
     */
    private $category;

    /**
     * @var \Shop\CatalogBundle\Entity\Parameter
     */
    private $parameter;

    /**
     * @param $collection \Weasty\Doctrine\Cache\Collection\CacheCollection
     * @return \Weasty\Doctrine\Cache\Collection\CacheCollectionElementInterface
     */
    public function createCollectionElement($collection)
    {
        return new CategoryParameterElement($collection, $this);
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
     * @param boolean $isComparable
     * @return $this
     */
    public function setIsComparable($isComparable)
    {
        $this->isComparable = $isComparable;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getIsComparable()
    {
        return $this->isComparable;
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
     * Set category
     *
     * @param \Shop\CatalogBundle\Entity\Category $category
     * @return CategoryParameter
     */
    public function setCategory(Category $category = null)
    {
        $this->category = $category;
        $this->setCategoryId($category->getId());
        $this->setPosition($category->getParameters()->count());
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
    /**
     * @var string
     */
    private $name;


    /**
     * Set name
     *
     * @param string $name
     * @return CategoryParameter
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
        return $this->name ?: ($this->getParameter() ? $this->getParameter()->getName() : null);
    }

    /**
     * Check name before entity update
     */
    public function checkName(){

        if($this->getParameter() && $this->getParameter()->getName() == $this->name){
            $this->name = null;
        }

    }

    /**
     * @var integer
     */
    private $groupId;

    /**
     * @var \Shop\CatalogBundle\Entity\CategoryParameterGroup
     */
    private $group;


    /**
     * Set groupId
     *
     * @param integer $groupId
     * @return CategoryParameter
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;

        return $this;
    }

    /**
     * Get groupId
     *
     * @return integer 
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * Set group
     *
     * @param \Shop\CatalogBundle\Entity\CategoryParameterGroup $group
     * @return CategoryParameter
     */
    public function setGroup(CategoryParameterGroup $group = null)
    {
        $this->group = $group;
        $this->groupId = $group ? $group->getId() : null;
        return $this;
    }

    /**
     * Get group
     *
     * @return \Shop\CatalogBundle\Entity\CategoryParameterGroup 
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Set filterType
     *
     * @param array $filterGroups
     * @return CategoryParameter
     */
    public function setFilterGroups($filterGroups)
    {
        $this->filterGroups = $filterGroups;

        return $this;
    }

    /**
     * Get filterType
     *
     * @return array
     */
    public function getFilterGroups()
    {
        return $this->filterGroups;
    }
}
