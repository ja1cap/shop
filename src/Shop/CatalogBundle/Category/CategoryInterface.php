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
     * @return \Doctrine\Common\Collections\Collection|array
     */
    public function getParameters();

    /**
     * Get parameterGroups
     *
     * @return \Doctrine\Common\Collections\Collection|array
     */
    public function getParameterGroups();

} 