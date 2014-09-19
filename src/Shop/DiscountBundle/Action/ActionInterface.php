<?php
namespace Shop\DiscountBundle\Action;
use Shop\DiscountBundle\ActionCondition\ActionConditionInterface;
use Weasty\Doctrine\Entity\EntityInterface;

/**
 * Interface ActionInterface
 * @package Shop\DiscountBundle\Action
 */
interface ActionInterface extends EntityInterface {

    const STATUS_ON = 1;
    const STATUS_OFF = 2;

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

} 