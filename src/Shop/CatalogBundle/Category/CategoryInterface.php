<?php
namespace Shop\CatalogBundle\Category;

use Weasty\Bundle\CatalogBundle\Category\CategoryInterface as BaseCategoryInterface;

/**
 * Interface CategoryInterface
 * @package Shop\CatalogBundle\Category
 */
interface CategoryInterface extends BaseCategoryInterface {

    /**
     * Get parameters
     *
     * @return \Doctrine\Common\Collections\Collection|\Shop\CatalogBundle\Category\Parameter\CategoryParameterInterface[]
     */
    public function getParameters();

    /**
     * Get parameterGroups
     *
     * @return \Doctrine\Common\Collections\Collection|\Shop\CatalogBundle\Category\Parameter\CategoryParameterGroupInterface[]
     */
    public function getParameterGroups();

} 