<?php
namespace Shop\DiscountBundle\Data;
use Shop\DiscountBundle\Entity\ActionConditionInterface;

/**
 * Class ActionConditionResource
 * @package Shop\DiscountBundle\Data
 */
class ActionConditionResource {

    /**
     * @var array
     */
    public static $types = array(
        ActionConditionInterface::TYPE_COLLECTION => 'Группа',
        ActionConditionInterface::TYPE_SET => 'Комплект',
    );

    /**
     * @var array
     */
    public static $discountTypes = array(
        ActionConditionInterface::DISCOUNT_TYPE_PERCENT => 'Скидка в процентах',
        ActionConditionInterface::DISCOUNT_TYPE_PRICE => 'Акционная цена',
        ActionConditionInterface::DISCOUNT_TYPE_GIFT => 'Подарок',
        ActionConditionInterface::DISCOUNT_TYPE_GIFT_AND_PERCENT => 'Подарок и скидка в процентах',
        ActionConditionInterface::DISCOUNT_TYPE_GIFT_OR_PERCENT => 'Подарок или скидка в процентах',
        ActionConditionInterface::DISCOUNT_TYPE_GIFT_AND_PRICE => 'Подарок и акционная цена',
        ActionConditionInterface::DISCOUNT_TYPE_GIFT_OR_PRICE => 'Подарок или акционная цена',
    );

} 