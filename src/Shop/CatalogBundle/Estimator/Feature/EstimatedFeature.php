<?php
namespace Shop\CatalogBundle\Estimator\Feature;

use Weasty\Bundle\CatalogBundle\Feature\Feature;
use Weasty\Bundle\CatalogBundle\Feature\FeatureValueInterface;

/**
 * Class EstimatedFeature
 * @package Shop\CatalogBundle\Estimator\Feature
 */
class EstimatedFeature extends Feature
    implements  EstimatedFeatureInterface
{

    /**
     * @var int
     */
    public $priorityOrder;

    /**
     * @var int
     */
    public $bestPriority;

    /**
     * @var bool
     */
    public $hasEqualValues = true;

    /**
     * @var bool
     */
    public $isComparable = true;

    /**
     * @return int
     */
    public function getPriorityOrder()
    {
        return $this->priorityOrder;
    }

    /**
     * @param int $priorityOrder
     * @return $this
     */
    public function setPriorityOrder($priorityOrder)
    {
        $this->priorityOrder = $priorityOrder;
        return $this;
    }

    /**
     * @return int
     */
    public function getBestPriority()
    {
        return $this->bestPriority;
    }

    /**
     * @param int $maxPriority
     * @return $this
     */
    public function setBestPriority($maxPriority)
    {
        $this->bestPriority = $maxPriority;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getHasEqualValues()
    {
        return $this->hasEqualValues;
    }

    /**
     * @param boolean $hasEqualValues
     * @return $this
     */
    public function setHasEqualValues($hasEqualValues)
    {
        $this->hasEqualValues = $hasEqualValues;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getIsComparable()
    {
        return $this->isComparable;
    }

    /**
     * @param boolean $isComparable
     * @return $this
     */
    public function setIsComparable($isComparable)
    {
        $this->isComparable = $isComparable;
        return $this;
    }

    /**
     * @param $key
     * @param FeatureValueInterface $value
     * @return $this
     */
    public function addFeatureValue($key, $value)
    {

        if($value instanceof EstimatedFeatureValue){

            if($this->getIsComparable()){

                switch($this->getPriorityOrder()){
                    case self::PRIORITY_ORDER_DESC:

                        if($this->bestPriority === null || $value->getPriority() < $this->bestPriority){
                            $this->bestPriority = $value->getPriority();
                        }
                        break;

                    case self::PRIORITY_ORDER_ASC:
                    default:

                        if($value->getPriority() > $this->bestPriority){
                            $this->bestPriority = $value->getPriority();
                        }

                }

                if($this->featureValues && $this->hasEqualValues){

                    $lastValue = end($this->featureValues);

                    if($lastValue instanceof FeatureValueInterface){

                        if($lastValue->getValue() != $value->getValue()){

                            $this->hasEqualValues = false;

                        }

                    }

                }

            }

            return parent::addFeatureValue($key, $value);

        }

        return $this;

    }

}