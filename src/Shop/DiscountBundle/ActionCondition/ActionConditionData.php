<?php
namespace Shop\DiscountBundle\ActionCondition;

/**
 * Class ActionConditionData
 * @package Shop\DiscountBundle\ActionCondition
 */
class ActionConditionData {

    /**
     * @var array
     */
    private static $types = [
        ActionConditionInterface::TYPE_DISCOUNT_PERCENT => 'Скидка в процентах',
        ActionConditionInterface::TYPE_DISCOUNT_PRICE => 'Акционная цена',
        ActionConditionInterface::TYPE_GIFT => 'Подарок',
        ActionConditionInterface::TYPE_GIFT_AND_DISCOUNT_PERCENT => 'Подарок и скидка в процентах',
        ActionConditionInterface::TYPE_GIFT_OR_DISCOUNT_PERCENT => 'Подарок или скидка в процентах',
        ActionConditionInterface::TYPE_GIFT_AND_DISCOUNT_PRICE => 'Подарок и акционная цена',
        ActionConditionInterface::TYPE_GIFT_OR_DISCOUNT_PRICE => 'Подарок или акционная цена',
    ];

    /**\
     * @return array
     */
    public static function getTypes(){
        return self::$types;
    }

    /**
     * @return array
     */
    public static function getDiscountTypes(){
        return [
            ActionConditionInterface::TYPE_DISCOUNT_PRICE,
            ActionConditionInterface::TYPE_GIFT_AND_DISCOUNT_PRICE,
            ActionConditionInterface::TYPE_GIFT_OR_DISCOUNT_PRICE,
            ActionConditionInterface::TYPE_DISCOUNT_PERCENT,
            ActionConditionInterface::TYPE_GIFT_AND_DISCOUNT_PERCENT,
            ActionConditionInterface::TYPE_GIFT_OR_DISCOUNT_PERCENT,
        ];
    }

} 