<?php

namespace Shop\DiscountBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Shop\DiscountBundle\Action\ActionInterface;

/**
 * Class ActionCondition
 * @package Shop\DiscountBundle\Entity
 */
abstract class ActionCondition extends AbstractActionCondition
{

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var \Shop\DiscountBundle\Entity\Action
     */
    protected $action;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $gifts;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->gifts = new ArrayCollection();
    }

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
     * Set action
     *
     * @param ActionInterface $action
     * @return ActionCategory
     */
    public function setAction(ActionInterface $action = null)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get action
     *
     * @return \Shop\DiscountBundle\Entity\Action
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Create gift
     *
     * @return \Shop\DiscountBundle\Entity\AbstractActionConditionGift
     */
    public function createGift()
    {
        return new ActionConditionGift();
    }

    /**
     * Add gift
     *
     * @param \Shop\DiscountBundle\Entity\AbstractActionConditionGift $gift
     * @return ActionCondition
     */
    public function addGift(AbstractActionConditionGift $gift)
    {
        $this->gifts[] = $gift;

        return $this;
    }

    /**
     * Remove gift
     *
     * @param \Shop\DiscountBundle\Entity\AbstractActionConditionGift $gift
     */
    public function removeGift(AbstractActionConditionGift $gift)
    {
        $this->gifts->removeElement($gift);
    }

    /**
     * Get gifts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGifts()
    {
        return $this->gifts;
    }
}
