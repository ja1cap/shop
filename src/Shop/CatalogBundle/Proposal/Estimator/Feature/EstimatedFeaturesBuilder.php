<?php
namespace Shop\CatalogBundle\Proposal\Estimator\Feature;

use Shop\CatalogBundle\Category\Parameter\CategoryParameterInterface;
use Shop\CatalogBundle\Proposal\Feature\FeaturesBuilder;
use Weasty\Bundle\CatalogBundle\Parameter\Value\ParameterValueInterface;

/**
 * Class EstimatedFeaturesBuilder
 * @package Shop\CatalogBundle\Proposal\Estimator\Feature
 */
class EstimatedFeaturesBuilder extends FeaturesBuilder {

    /**
     * @var \Shop\CatalogBundle\Proposal\Estimator\Estimator
     */
    protected $estimator;

    /**
     * @return \Weasty\Bundle\CatalogBundle\Feature\FeatureInterface
     */
    protected function createFeature()
    {
        return new EstimatedFeature();
    }

    /**
     * @param CategoryParameterInterface $categoryParameter
     * @param ParameterValueInterface|null $parameterValue
     * @return \Weasty\Bundle\CatalogBundle\Feature\FeatureInterface
     */
    protected function buildFeature(CategoryParameterInterface $categoryParameter, ParameterValueInterface $parameterValue = null)
    {

        $feature = parent::buildFeature($categoryParameter, $parameterValue);

        /**
         * @var EstimatedFeature $feature
         */
        $feature
            ->setEstimator($this->getEstimator())
        ;

        if($parameterValue){

            if($feature->getWeight() > $this->getEstimator()->getParameterBestWeight($parameterValue->getParameterId())){
                $this->getEstimator()->setParameterBestWeight($parameterValue->getParameterId(), $feature->getWeight());
            }

        }

        return $feature;

    }

    /**
     * @return \Shop\CatalogBundle\Proposal\Estimator\Estimator
     */
    public function getEstimator()
    {
        if(!$this->estimator){
            //@TODO throw exception - estimator not defined
        }
        return $this->estimator;
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