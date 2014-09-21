<?php
namespace Shop\DiscountBundle\ActionCondition;

use Shop\DiscountBundle\Action\ActionInterface;
use Shop\DiscountBundle\Gift\ActionConditionGiftInterface;
use Weasty\Doctrine\Cache\Collection\CacheCollection;
use Weasty\Doctrine\Cache\Collection\CacheCollectionElement;
use Weasty\Doctrine\Entity\EntityInterface;

/**
 * Class ActionConditionElement
 * @package Shop\DiscountBundle\ActionCondition
 */
abstract class ActionConditionElement extends CacheCollectionElement
    implements ActionConditionInterface
{

    /**
     * @var array
     */
    public $giftIds;

    /**
     * @var \Shop\DiscountBundle\Gift\ActionConditionGiftInterface[]
     */
    private $gifts;

    /**
     * @var \Shop\DiscountBundle\Action\ActionInterface
     */
    protected $action;

    /**
     * @param CacheCollection $collection
     * @param EntityInterface $entity
     * @return array
     */
    protected function buildData(CacheCollection $collection, EntityInterface $entity)
    {

        $data = parent::buildData($collection, $entity);

        if($entity instanceof ActionConditionInterface){

            $data['isGiftCondition'] = $entity->getIsGiftCondition();
            $data['isDiscountCondition'] = $entity->getIsDiscountCondition();

            $giftCollection = $collection->getCollectionManager()->getCollection('ShopDiscountBundle:AbstractActionConditionGift');
            foreach($entity->getGifts() as $gift){

                $giftElement = $giftCollection->saveElement($gift);
                if(!$giftElement){
                    continue;
                }

                $this->giftIds[] = $gift->getId();
                $this->gifts[] = $giftElement;

            }

        }

        return $data;

    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->getIdentifier();
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->data['type'];
    }

    /**
     * @return ActionConditionInterface
     */
    public function getParentCondition()
    {
        return ($this->getType() == self::TYPE_INHERIT && $this->getAction() ? $this->getAction()->getBasicCondition() : null);
    }

    /**
     * @return integer
     */
    public function getPriority()
    {
        return $this->data['priority'];
    }

    /**
     * @return boolean
     */
    public function getIsComplex()
    {
        return $this->data['isComplex'];
    }

    /**
     * @return int
     */
    public function getActionId()
    {
        return $this->data['actionId'];
    }

    /**
     * @param \Shop\DiscountBundle\Action\ActionInterface $action
     * @return $this
     */
    public function setAction(ActionInterface $action)
    {
        $this->getEntity()->setAction($action);
        return $this;
    }

    /**
     * @return \Shop\DiscountBundle\Action\ActionInterface
     */
    public function getAction()
    {
        if(!$this->action){
            $this->action = $this->getCollectionManager()->getCollection('ShopDiscountBundle:Action')->get($this->getActionId());
        }
        return $this->action;
    }

    /**
     * @return boolean
     */
    public function getIsGiftCondition()
    {
        return $this->data['isGiftCondition'];
    }

    /**
     * @return boolean
     */
    public function getIsDiscountCondition()
    {
        return $this->data['isDiscountCondition'];
    }

    /**
     * Get discountPriceValue
     *
     * @return float
     */
    public function getDiscountPriceValue()
    {
        return $this->data['discountPriceValue'];
    }

    /**
     * Get discountPriceCurrencyNumericCode
     *
     * @return integer
     */
    public function getDiscountPriceCurrencyNumericCode()
    {
        return $this->data['discountPriceCurrencyNumericCode'];
    }

    /**
     * Get discountPercent
     *
     * @return float
     */
    public function getDiscountPercent()
    {
        return $this->data['discountPercent'];
    }

    /**
     * @return \Doctrine\Common\Collections\Collection|\Shop\DiscountBundle\Entity\AbstractActionConditionGift[]
     */
    public function getGifts()
    {

        if(!$this->gifts && $this->giftIds){

            $giftCollection = $this->getCollectionManager()->getCollection('ShopDiscountBundle:AbstractActionConditionGift');

            foreach($this->giftIds as $giftId){

                $gift = $giftCollection->get($giftId);

                if($gift instanceof ActionConditionGiftInterface){
                    $this->gifts[] = $gift;
                }

            }

        }

        return $this->gifts;

    }

    /**
     * Get gift proposals
     * @return \Shop\CatalogBundle\Proposal\ProposalInterface[]
     */
    public function getGiftProposals()
    {
        $proposals = [];
        foreach ($this->getGifts() as $gift) {
            $proposals[] = $gift->getProposal();
        }
        return $proposals;
    }

    /**
     * Get \Shop\CatalogBundle\Proposal\ProposalInterface ids
     * @return array
     */
    public function getGiftProposalIds()
    {
        $proposalIds = [];
        foreach ($this->getGifts() as $gift) {
            $proposalIds[] = $gift->getProposalId();
        }
        return $proposalIds;
    }

    /**
     * @return ActionConditionInterface
     * @throws \Weasty\Doctrine\Cache\Collection\Exception\CacheCollectionException
     */
    public function getEntity()
    {
        return parent::getEntity();
    }

} 