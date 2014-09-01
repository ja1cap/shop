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
     * @var array
     */
    protected $parameterBestWeights = [];

    /**
     * @var EstimatedProposal[]
     */
    protected $proposals;

    /**
     * @param int $parameterId
     * @return int
     */
    public function getParameterBestWeight($parameterId){

        if(isset($this->parameterBestWeights[$parameterId])){
            return $this->parameterBestWeights[$parameterId];
        }

        return 0;

    }

    /**
     * @param int $parameterId
     * @param int $weight
     * @return int
     */
    public function setParameterBestWeight($parameterId, $weight){
        return $this->parameterBestWeights[$parameterId] = (int)$weight;
    }

    /**
     * @return EstimatedProposal[]
     */
    public function getProposals()
    {
        return $this->proposals;
    }

    /**
     * @param EstimatedProposal[] $proposals
     * @return $this
     */
    public function setProposals($proposals)
    {
        $this->proposals = $proposals;
        return $this;
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

} 