<?php
namespace Shop\CatalogBundle\Proposal\Estimator;

/**
 * Class Estimator
 * @package Shop\CatalogBundle\Proposal\Estimator
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

} 