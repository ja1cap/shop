<?php
namespace Shop\CatalogBundle\Proposal\Estimator\Feature;

use Shop\CatalogBundle\Category\Parameter\CategoryParameterInterface;
use Shop\CatalogBundle\Proposal\Feature\FeaturesBuilder;
use Shop\CatalogBundle\Proposal\Price\ProposalPriceInterface;
use Weasty\Bundle\CatalogBundle\Feature\FeatureInterface;
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
     * @return \Shop\CatalogBundle\Proposal\Estimator\Feature\EstimatedFeatureInterface
     */
    protected function createFeature()
    {
        return new EstimatedFeature();
    }

    /**
     * @param CategoryParameterInterface $categoryParameter
     * @return FeatureInterface
     */
    protected function buildFeature(CategoryParameterInterface $categoryParameter)
    {

        $feature = parent::buildFeature($categoryParameter);

        if($feature instanceof EstimatedFeatureInterface){
            $feature->setIsComparable($categoryParameter->getIsComparable());
        }

        return $feature;

    }

    /**
     * @return EstimatedFeatureValue|\Weasty\Bundle\CatalogBundle\Proposal\Feature\ProposalFeatureValueInterface
     */
    protected function createFeatureValue()
    {
        return new EstimatedFeatureValue();
    }

    /**
     * @param \Weasty\Bundle\CatalogBundle\Feature\FeatureInterface $feature
     * @param \Shop\CatalogBundle\Proposal\Price\ProposalPriceInterface $price
     * @param \Weasty\Bundle\CatalogBundle\Parameter\Value\ParameterValueInterface $parameterValue
     * @return \Weasty\Bundle\CatalogBundle\Proposal\Feature\ProposalFeatureValueInterface
     */
    protected function buildFeatureValue(FeatureInterface $feature, ProposalPriceInterface $price, ParameterValueInterface $parameterValue)
    {
        $featureValue = parent::buildFeatureValue($feature, $price, $parameterValue);

        if($this->getEstimator() || $featureValue instanceof EstimatedFeatureValue){

            $featureValue->setPriority($parameterValue->getOption()->getPriority());

            $estimatedProposal = $this->getEstimator()->getEstimatedProposal($featureValue->getPriceId());
            if($estimatedProposal){
                $estimatedProposal->addFeatureValue($featureValue);
            }

        }

        return $featureValue;
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