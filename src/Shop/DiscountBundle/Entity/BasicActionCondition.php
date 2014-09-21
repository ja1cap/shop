<?php
namespace Shop\DiscountBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Shop\DiscountBundle\Action\ActionInterface;
use Shop\DiscountBundle\ActionCondition\BasicActionConditionElement;

/**
 * Class BasicActionCondition
 * @package Shop\DiscountBundle\Entity
 */
class BasicActionCondition extends AbstractActionCondition {

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
        $this->priority = 1;
        $this->gifts = new ArrayCollection();
    }

    /**
     * @param $collection \Weasty\Doctrine\Cache\Collection\CacheCollection
     * @return \Weasty\Doctrine\Cache\Collection\CacheCollectionElementInterface
     */
    public function createCollectionElement($collection)
    {
        return new BasicActionConditionElement($collection, $this);
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
     * @return BasicActionCondition
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
        return new BasicActionConditionGift();
    }

    /**
     * Add gift
     *
     * @param \Shop\DiscountBundle\Entity\AbstractActionConditionGift $gift
     * @return BasicActionCondition
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
