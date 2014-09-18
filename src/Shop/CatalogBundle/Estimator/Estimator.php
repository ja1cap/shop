<?php
namespace Shop\CatalogBundle\Estimator;

/**
 * Class Estimator
 * @package Shop\CatalogBundle\Estimator
 */
class Estimator {

    /**
     * @var \Shop\CatalogBundle\Category\CategoryInterface
     */
    protected $category;

    /**
     * @var EstimatedProposal[]
     */
    protected $estimatedProposals;

    /**
     * @var \Weasty\Bundle\CatalogBundle\Feature\FeaturesResourceInterface
     */
    protected $estimatedFeatures;

    /**
     * @var \Shop\CatalogBundle\Estimator\Feature\EstimatedFeatureInterface
     */
    protected $priceFeature;

    /**
     * @var \Shop\CatalogBundle\Estimator\Feature\EstimatedFeatureInterface
     */
    protected $rateFeature;

    /**
     * @return EstimatedProposal[]
     */
    public function getEstimatedProposals()
    {
        return $this->estimatedProposals;
    }

    /**
     * @param $priceId
     * @param EstimatedProposal $estimatedProposal
     * @return $this
     */
    public function addEstimatedProposal($priceId, EstimatedProposal $estimatedProposal){

        $this->estimatedProposals[$priceId] = $estimatedProposal;
        return $this;

    }

    /**
     * @param $priceId
     * @return null|EstimatedProposal
     */
    public function getEstimatedProposal($priceId){

        if(isset($this->estimatedProposals[$priceId])){
            return $this->estimatedProposals[$priceId];
        }

        return null;

    }

    /**
     * @return \Shop\CatalogBundle\Category\CategoryInterface
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param \Shop\CatalogBundle\Category\CategoryInterface $category
     * @return $this
     */
    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return \Weasty\Bundle\CatalogBundle\Feature\FeaturesResourceInterface
     */
    public function getEstimatedFeatures()
    {
        return $this->estimatedFeatures;
    }

    /**
     * @param \Weasty\Bundle\CatalogBundle\Feature\FeaturesResourceInterface $estimatedFeatures
     * @return $this
     */
    public function setEstimatedFeatures($estimatedFeatures)
    {
        $this->estimatedFeatures = $estimatedFeatures;
        return $this;
    }

    /**
     * @return Feature\EstimatedFeatureInterface
     */
    public function getPriceFeature()
    {
        return $this->priceFeature;
    }

    /**
     * @param Feature\EstimatedFeatureInterface $priceFeature
     * @return $this
     */
    public function setPriceFeature($priceFeature)
    {
        $this->priceFeature = $priceFeature;
        return $this;
    }

    /**
     * @return Feature\EstimatedFeatureInterface
     */
    public function getRateFeature()
    {
        return $this->rateFeature;
    }

    /**
     * @param Feature\EstimatedFeatureInterface $rateFeature
     * @return $this
     */
    public function setRateFeature($rateFeature)
    {
        $this->rateFeature = $rateFeature;
        return $this;
    }

    /**
     * @return $this
     */
    public function updateRateFeatureValues(){

        //@TODO add Feature\EstimatedFeatureInterface::resetValues()
        foreach($this->getEstimatedProposals() as $estimatedProposal){

            $featureValue = $estimatedProposal->getRateFeatureValue();
            $this->getRateFeature()->addFeatureValue($featureValue->getPriceId(), $featureValue);

        }

        return $this;

    }

} 