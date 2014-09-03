<?php
namespace Shop\CatalogBundle\Proposal\Estimator;
use Shop\CatalogBundle\Proposal\Estimator\Feature\EstimatedFeatureValue;

/**
 * Class EstimatorProposal
 * @package Shop\CatalogBundle\Estimator
 */
class EstimatedProposal {

    /**
     * @var \Shop\CatalogBundle\Proposal\ProposalInterface
     */
    protected $proposal;

    /**
     * @var \Shop\CatalogBundle\Proposal\Price\ProposalPriceInterface
     */
    protected $price;

    /**
     * @var \Shop\CatalogBundle\Proposal\Estimator\Feature\EstimatedFeatureValue[]
     */
    protected $featureValues;

    /**
     * @var int
     */
    protected $rate;

    function __construct($proposal, $price)
    {
        $this->proposal = $proposal;
        $this->price = $price;
        $this->featureValues = [];
        $this->rate = 0;
    }

    /**
     * @return \Shop\CatalogBundle\Proposal\ProposalInterface
     */
    public function getProposal()
    {
        return $this->proposal;
    }

    /**
     * @return \Shop\CatalogBundle\Proposal\Price\ProposalPriceInterface
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param EstimatedFeatureValue $value
     * @return $this
     */
    public function addFeatureValue(EstimatedFeatureValue $value){
        $this->featureValues[$value->getFeature()->getId()] = $value;
        return $this;
    }

    /**
     * @param $featureId
     * @return $this
     */
    public function removeFeatureValue($featureId){
        if(isset($this->featureValues[$featureId])){
            unset($this->featureValues[$featureId]);
        }
        return $this;
    }

    /**
     * @param $featureId
     * @return \Shop\CatalogBundle\Proposal\Estimator\Feature\EstimatedFeatureValue|null
     */
    public function getFeatureValue($featureId){
        if(isset($this->featureValues[$featureId])){
            return $this->featureValues[$featureId];
        }
        return null;
    }

    /**
     * @return \Shop\CatalogBundle\Proposal\Estimator\Feature\EstimatedFeatureValue[]
     */
    public function getFeatureValues()
    {
        return $this->featureValues;
    }

    /**
     * @return $this
     */
    public function incrementRate(){
        $this->rate++;
        return $this;
    }

    /**
     * @return int
     */
    public function getRate()
    {
        return $this->rate;
    }

} 