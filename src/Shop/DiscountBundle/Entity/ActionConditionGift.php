<?php

namespace Shop\DiscountBundle\Entity;

/**
 * Class ActionConditionGift
 * @package Shop\DiscountBundle\Entity
 */
class ActionConditionGift extends AbstractActionConditionGift
{

    /**
     * @var \Shop\DiscountBundle\Entity\AbstractActionCondition
     */
    protected $condition;

    /**
     * Set condition
     *
     * @param \Shop\DiscountBundle\Entity\AbstractActionCondition $condition
     * @return ActionConditionGift
     */
    public function setCondition(AbstractActionCondition $condition = null)
    {
        $this->condition = $condition;
        $this->conditionId = $condition->getId();
        return $this;
    }

    /**
     * Get condition
     *
     * @return \Shop\DiscountBundle\Entity\AbstractActionCondition
     */
    public function getCondition()
    {
        return $this->condition;
    }

}
