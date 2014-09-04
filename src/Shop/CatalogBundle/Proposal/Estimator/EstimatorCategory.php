<?php
namespace Shop\CatalogBundle\Proposal\Estimator;

/**
 * Class EstimatorCategory
 * @package Shop\CatalogBundle\Proposal\Estimator
 */
class EstimatorCategory {

    /**
     * @var \Shop\CatalogBundle\Proposal\Estimator\Data\CategoryEstimationData
     */
    protected $categoryEstimationData;

    /**
     * @var \Shop\CatalogBundle\Category\CategoryInterface
     */
    protected $category;

    /**
     * @var \Shop\CatalogBundle\Proposal\Estimator\EstimatorProposal[]
     */
    protected $proposals;

    /**
     * @var EstimatorBuilder
     */
    protected $estimatorBuilder;

    function __construct($category, $categoryEstimationData, $estimatorBuilder)
    {
        $this->category = $category;
        $this->categoryEstimationData = $categoryEstimationData;
        $this->estimatorBuilder = $estimatorBuilder;
        $this->proposals = [];
    }

    /**
     * @return int
     */
    public function getProposalsAmount(){

        if($this->proposals){
            $amount = count($this->proposals);
        } else {
            $amount = count($this->categoryEstimationData->getProposalPrices());
        }

        return $amount;

    }

    /**
     * @return \Shop\CatalogBundle\Category\CategoryInterface
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @return Data\CategoryEstimationData
     */
    public function getCategoryEstimationData()
    {
        return $this->categoryEstimationData;
    }

    /**
     * @return EstimatorProposal[]
     */
    public function getProposals()
    {
        if(!$this->proposals){
            $this->proposals = $this->estimatorBuilder->buildEstimatorCategoryProposals($this);
        }
        return $this->proposals;
    }

    /**
     * @param EstimatorProposal[] $proposals
     */
    public function setProposals($proposals)
    {
        $this->proposals = $proposals;
    }


} 