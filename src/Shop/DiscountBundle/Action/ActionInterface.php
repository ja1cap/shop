<?php
namespace Shop\DiscountBundle\Action;

use Shop\DiscountBundle\ActionCondition\ActionConditionInterface;

/**
 * Interface ActionInterface
 * @package Shop\DiscountBundle\Action
 */
interface ActionInterface {

    const STATUS_ON = 1;
    const STATUS_OFF = 2;

    /**
     * @return int
     */
    public function getId();

    /**
     * @return int
     */
    public function getStatus();

    /**
     * @param integer $value
     * @return $this
     */
    public function setPosition($value);

    /**
     * @return integer
     */
    public function getPosition();

    /**
     * Get basicCondition
     *
     * @return \Shop\DiscountBundle\Entity\BasicActionCondition
     */
    public function getBasicCondition();

    /**
     * Add conditions
     *
     * @param \Shop\DiscountBundle\ActionCondition\ActionConditionInterface $condition
     * @return $this
     */
    public function addCondition(ActionConditionInterface $condition);

    /**
     * Get conditions
     *
     * @return \Doctrine\Common\Collections\Collection|\Shop\DiscountBundle\ActionCondition\ActionConditionInterface[]
     */
    public function getConditions();

    /**
     * @return \Shop\DiscountBundle\Category\ActionCategoryInterface[]
     */
    public function getActionCategories();

    /**
     * Get \Shop\CatalogBundle\Entity\Category ids
     * @return array
     */
    public function getCategoryIds();

    /**
     * @return \Shop\DiscountBundle\Proposal\ActionProposalInterface[]
     */
    public function getActionProposals();

    /**
     * Get \Shop\CatalogBundle\Entity\Proposal ids
     * @return array
     */
    public function getProposalIds();

}