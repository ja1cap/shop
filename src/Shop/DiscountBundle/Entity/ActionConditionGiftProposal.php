<?php

namespace Shop\DiscountBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Weasty\Doctrine\Entity\AbstractEntity;

/**
 * Class ActionConditionGiftProposal
 * @package Shop\DiscountBundle\Entity
 */
class ActionConditionGiftProposal extends AbstractEntity
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
    private $proposalId;

    /**
     * @var \Shop\DiscountBundle\Entity\ActionCondition
     */
    private $condition;

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
     * @return ActionConditionGiftProposal
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
     * Set proposalId
     *
     * @param integer $proposalId
     * @return ActionConditionGiftProposal
     */
    public function setProposalId($proposalId)
    {
        $this->proposalId = $proposalId;

        return $this;
    }

    /**
     * Get proposalId
     *
     * @return integer 
     */
    public function getProposalId()
    {
        return $this->proposalId;
    }

    /**
     * Set condition
     *
     * @param \Shop\DiscountBundle\Entity\ActionCondition $condition
     * @return ActionConditionGiftProposal
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
