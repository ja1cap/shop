<?php
namespace Shop\DiscountBundle\Category;

use Shop\DiscountBundle\ActionCondition\ActionConditionElement;

/**
 * Class ActionCategoryElement
 * @package Shop\DiscountBundle\Category
 */
class ActionCategoryElement extends ActionConditionElement
    implements ActionCategoryInterface
{

    /**
     * @return int
     */
    public function getCategoryId()
    {
        return $this->data['categoryId'];
    }

} 