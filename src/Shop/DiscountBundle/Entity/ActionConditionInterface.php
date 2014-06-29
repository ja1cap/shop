<?php
namespace Shop\DiscountBundle\Entity;

use Weasty\Doctrine\Entity\EntityInterface;

/**
 * Interface ActionConditionInterface
 * @package Shop\DiscountBundle\Data
 */
interface ActionConditionInterface extends EntityInterface {

    const TYPE_COLLECTION = 1;
    const TYPE_SET = 2;

    const DISCOUNT_TYPE_PERCENT = 1;
    const DISCOUNT_TYPE_PRICE = 2;
    const DISCOUNT_TYPE_GIFT = 3;
    const DISCOUNT_TYPE_GIFT_AND_PERCENT = 4;
    const DISCOUNT_TYPE_GIFT_OR_PERCENT = 5;
    const DISCOUNT_TYPE_GIFT_AND_PRICE = 6;
    const DISCOUNT_TYPE_GIFT_OR_PRICE = 7;

    /**
     * @return int
     */
    public function getType();

    /**
     * @return int
     */
    public function getActionId();

    /**
     * @param ActionInterface $action
     * @return $this
     */
    public function setAction(ActionInterface $action);

    /**
     * @return \Shop\DiscountBundle\Entity\ActionInterface
     */
    public function getAction();

} 