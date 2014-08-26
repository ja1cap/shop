<?php
namespace Shop\DiscountBundle\Element;

use Shop\DiscountBundle\Entity\ActionCondition;
use Shop\DiscountBundle\Entity\ActionConditionInterface;
use Shop\DiscountBundle\Entity\ActionInterface;
use Weasty\Doctrine\Cache\Collection\CacheCollection;
use Weasty\Doctrine\Cache\Collection\CacheCollectionElement;
use Weasty\Doctrine\Entity\EntityInterface;

/**
 * Class ActionConditionElement
 * @package Shop\DiscountBundle\CollectionElement
 */
class ActionConditionElement extends CacheCollectionElement
    implements ActionConditionInterface
{

    /**
     * @var \Shop\DiscountBundle\Entity\ActionInterface
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
            $data['isPriceDiscount'] = $entity->getIsPriceDiscount();
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
     * @return int
     */
    public function getType()
    {
        return $this->data['type'];
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
     * @param ActionInterface $action
     * @return $this
     */
    public function setAction(ActionInterface $action)
    {
        $this->getEntity()->setAction($action);
        return $this;
    }

    /**
     * @return \Shop\DiscountBundle\Entity\ActionInterface
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
    public function getIsPriceDiscount()
    {
        return $this->data['isPriceDiscount'];
    }

    /**
     * Get discountType
     *
     * @return integer
     */
    public function getDiscountType()
    {
        return $this->data['discountType'];
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
     * @return ActionCondition
     * @throws \Weasty\Doctrine\Cache\Collection\Exception\CacheCollectionException
     */
    public function getEntity()
    {
        return parent::getEntity();
    }

} 