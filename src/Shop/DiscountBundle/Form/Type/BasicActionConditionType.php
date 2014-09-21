<?php
namespace Shop\DiscountBundle\Form\Type;
use Shop\DiscountBundle\ActionCondition\ActionConditionInterface;

/**
 * Class BasicActionConditionType
 * @package Shop\DiscountBundle\Form\Type
 */
class BasicActionConditionType extends ActionConditionType {

    /**
     * @return array
     */
    protected function getConditionTypeChoices()
    {
        $choices = parent::getConditionTypeChoices();
        unset($choices[ActionConditionInterface::TYPE_INHERIT]);
        return $choices;
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'shop_discount_basic_action_condition';
    }

} 