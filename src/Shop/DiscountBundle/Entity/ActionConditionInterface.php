<?php
namespace Shop\DiscountBundle\Entity;

/**
 * Interface ActionConditionInterface
 * @package Shop\DiscountBundle\Data
 */
interface ActionConditionInterface {

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
    public function getId();

    /**
     * @return int
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
     * @return \Shop\DiscountBundle\Entity\ActionInterface
     */
    public function getAction();

    /**
     * @return boolean
     */
    public function getIsPriceDiscount();

    /**
     * Get discountType
     *
     * @return integer
     */
    public function getDiscountType();

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
    public function getDiscountCategoryIds();

    /**
     * @return array
     */
    public function getProposalIds();

    /**
     * @return array
     */
    public function getDiscountProposalIds();

    }