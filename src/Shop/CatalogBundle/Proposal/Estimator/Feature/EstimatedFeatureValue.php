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

        if(!$feature->getIsComparable() || !$feature->getMaxPriority()){
            return false;
        }

        return ($this->getPriority() == $feature->getMaxPriority());
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
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

} 