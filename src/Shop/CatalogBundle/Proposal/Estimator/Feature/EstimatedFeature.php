<?php
namespace Shop\CatalogBundle\Proposal\Estimator\Feature;

use Weasty\Bundle\CatalogBundle\Feature\Feature;
use Weasty\Bundle\CatalogBundle\Feature\FeatureValueInterface;

/**
 * Class EstimatedFeature
 * @package Shop\CatalogBundle\Proposal\Estimator\Feature
 */
class EstimatedFeature extends Feature {

    /**
     * @var int
     */
    public $maxPriority;

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
    public function getMaxPriority()
    {
        return $this->maxPriority;
    }

    /**
     * @param int $maxPriority
     */
    public function setMaxPriority($maxPriority)
    {
        $this->maxPriority = $maxPriority;
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
     */
    public function setHasEqualValues($hasEqualValues)
    {
        $this->hasEqualValues = $hasEqualValues;
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
     */
    public function setIsComparable($isComparable)
    {
        $this->isComparable = $isComparable;
    }

    /**
     * @param $key
     * @param FeatureValueInterface $value
     * @return $this
     */
    public function addFeatureValue($key, $value)
    {

        if($this->getIsComparable() && $value instanceof EstimatedFeatureValue){

            if($value->getPriority() > $this->maxPriority){
                $this->maxPriority = $value->getPriority();
            }

            if($this->featureValues && $this->hasEqualValues){

                $lastValue = end($this->featureValues);

                if($lastValue instanceof FeatureValueInterface){

                    if($lastValue->getValue() != $value->getValue()){

                        $this->hasEqualValues = false;

                    }

                }

            }

            return parent::addFeatureValue($key, $value);

        }

        return $this;

    }

}