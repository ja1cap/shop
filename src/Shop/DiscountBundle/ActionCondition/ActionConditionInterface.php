<?php
namespace Shop\DiscountBundle\ActionCondition;

use Shop\DiscountBundle\Action\ActionInterface;

/**
 * Interface ActionConditionInterface
 * @package Shop\DiscountBundle\ActionCondition
 */
interface ActionConditionInterface {

    const TYPE_DISCOUNT_PERCENT = 1;
    const TYPE_DISCOUNT_PRICE = 2;
    const TYPE_GIFT = 3;
    const TYPE_GIFT_AND_DISCOUNT_PERCENT = 4;
    const TYPE_GIFT_OR_DISCOUNT_PERCENT = 5;
    const TYPE_GIFT_AND_DISCOUNT_PRICE = 6;
    const TYPE_GIFT_OR_DISCOUNT_PRICE = 7;

    /**
     * @return int
     */
    public function getId();

    /**
     * Get type
     *
     * @return integer
     */
    public function getType();

    /**
     * @return integer
     */
    public function getPriority();

    /**
     * @return boolean
     */
    public function getIsComplex();

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
     * @return \Shop\DiscountBundle\Action\ActionInterface
     */
    public function getAction();

    /**
     * @return boolean
     */
    public function getIsPriceDiscount();

    /**
     * Get discountPriceValue
     *
     * @return float
     */
    public function getDiscountPriceValue();

    /**
     * Get discountPriceCurrencyNumericCode
     *
     * @return integer
     */
    public function getDiscountPriceCurrencyNumericCode();

    /**
     * Get discountPercent
     *
     * @return float
     */
    public function getDiscountPercent();

    /**
     * @return array
     */
    public function getCategoryIds();

    /**
     * @return array
     */
    public function getProposalIds();

}