<?php
namespace Shop\DiscountBundle\Entity;
use Weasty\Doctrine\Entity\EntityInterface;

/**
 * Interface ActionInterface
 * @package Shop\DiscountBundle\Entity
 */
interface ActionInterface extends EntityInterface {

    /**
     * @param integer $value
     * @return $this
     */
    public function setPosition($value);

    /**
     * Add conditions
     *
     * @param \Shop\DiscountBundle\Entity\ActionConditionInterface $condition
     * @return $this
     */
    public function addCondition(ActionConditionInterface $condition);

    /**
     * Get conditions
     *
     * @return \Doctrine\Common\Collections\Collection|\Shop\DiscountBundle\Entity\ActionConditionInterface[]
     */
    public function getConditions();

} 