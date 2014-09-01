<?php
namespace Shop\CatalogBundle\Proposal\Estimator\Feature;

use Weasty\Bundle\CatalogBundle\Feature\Feature;

/**
 * Class EstimatedFeature
 * @package Shop\CatalogBundle\Proposal\Estimator\Feature
 */
class EstimatedFeature extends Feature {

    /**
     * @var \Shop\CatalogBundle\Proposal\Estimator\Estimator
     */
    protected $estimator;

    /**
     * @return bool
     */
    public function isBest(){

        $isBest = false;

        if($this->estimator && $this->id){

            $bestWeight = $this->estimator->getParameterBestWeight($this->id);

            if($bestWeight && $bestWeight == $this->getWeight()){
                $isBest = true;
            }

        }

        return $isBest;

    }

    /**
     * @param \Shop\CatalogBundle\Proposal\Estimator\Estimator $estimator
     * @return $this
     */
    public function setEstimator($estimator)
    {
        $this->estimator = $estimator;
        return $this;
    }

} 