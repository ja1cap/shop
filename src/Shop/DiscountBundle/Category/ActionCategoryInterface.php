<?php
namespace Shop\DiscountBundle\Category;

use Shop\DiscountBundle\ActionCondition\ActionConditionInterface;

/**
 * Interface ActionCategoryInterface
 * @package Shop\DiscountBundle\Category
 */
interface ActionCategoryInterface extends ActionConditionInterface {

    /**
     * @return int
     */
    public function getCategoryId();

} 