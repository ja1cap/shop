<?php

namespace Shop\DiscountBundle\Entity;
use Weasty\Doctrine\Entity\AbstractEntity;

/**
 * Class ActionConditionCategory
 * @package Shop\DiscountBundle\Entity
 */
class ActionConditionCategory extends AbstractEntity
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $conditionId;

    /**
     * @var integer
     */
    private $categoryId;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set conditionId
     *
     * @param integer $conditionId
     * @return ActionConditionCategory
     */
    public function setConditionId($conditionId)
    {
        $this->conditionId = $conditionId;

        return $this;
    }

    /**
     * Get conditionId
     *
     * @return integer 
     */
    public function getConditionId()
    {
        return $this->conditionId;
    }

    /**
     * Set categoryId
     *
     * @param integer $categoryId
     * @return ActionConditionCategory
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    /**
     * Get categoryId
     *
     * @return integer 
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }
    /**
     * @var \Shop\DiscountBundle\Entity\ActionCondition
     */
    private $condition;


    /**
     * Set condition
     *
     * @param \Shop\DiscountBundle\Entity\ActionCondition $condition
     * @return ActionConditionCategory
     */
    public function setCondition(ActionCondition $condition = null)
    {
        $this->condition = $condition;
        $this->conditionId = $condition->getId();
        return $this;
    }

    /**
     * Get condition
     *
     * @return \Shop\DiscountBundle\Entity\ActionCondition 
     */
    public function getCondition()
    {
        return $this->condition;
    }
}
