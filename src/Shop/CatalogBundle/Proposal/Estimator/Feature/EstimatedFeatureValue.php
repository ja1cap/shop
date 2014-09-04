<?php
namespace Shop\CatalogBundle\Proposal\Estimator\Feature;
use Weasty\Bundle\CatalogBundle\Proposal\Feature\ProposalFeatureValue;

/**
 * Class EstimatedFeatureValue
 * @package Shop\CatalogBundle\Proposal\Estimator\Feature
 */
class EstimatedFeatureValue extends ProposalFeatureValue {

    /**
     * @var int
     */
    public $priority;

    /**
     * @return bool
     */
    public function getIsBest(){

        $feature = $this->getFeature();

        if(!$feature instanceof EstimatedFeature){
            return false;
        }

        if($feature->getHasEqualValues() || !$feature->getIsComparable() || !$feature->getBestPriority()){
            return false;
        }

        return ($this->getPriority() == $feature->getBestPriority());
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param int $priority
     * @return $this
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
        return $this;
    }

} 