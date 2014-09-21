<?php
namespace Shop\DiscountBundle\Entity;

use Shop\CatalogBundle\Entity\Category;
use Shop\DiscountBundle\Category\ActionCategoryElement;
use Shop\DiscountBundle\Category\ActionCategoryInterface;

/**
 * Class ActionCategory
 * @package Shop\DiscountBundle\Entity
 */
class ActionCategory extends ActionCondition
    implements ActionCategoryInterface
{

    /**
     * @var integer
     */
    private $categoryId;

    /**
     * @var \Shop\CatalogBundle\Entity\Category
     */
    private $category;

    public function __construct()
    {
        parent::__construct();
        $this->priority = 2;
    }

    /**
     * @param $collection \Weasty\Doctrine\Cache\Collection\CacheCollection
     * @return \Weasty\Doctrine\Cache\Collection\CacheCollectionElementInterface
     */
    public function createCollectionElement($collection)
    {
        return new ActionCategoryElement($collection, $this);
    }

    /**
     * Set categoryId
     *
     * @param integer $categoryId
     * @return ActionCategory
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    /**
     * @return int
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * Set category
     *
     * @param \Shop\CatalogBundle\Entity\Category $category
     * @return ActionCategory
     */
    public function setCategory(Category $category = null)
    {
        $this->category = $category;

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

}
