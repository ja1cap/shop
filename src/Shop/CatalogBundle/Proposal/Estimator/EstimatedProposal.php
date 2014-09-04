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
     * @var \Shop\CatalogBundle\Proposal\Estimator\Feature\Price\PriceFeatureValue
     */
    protected $priceFeatureValue;

    /**
     * @var \Shop\CatalogBundle\Proposal\Estimator\Feature\EstimatedFeatureValue
     */
    protected $rateFeatureValue;

    /**
     * @var \Shop\CatalogBundle\Proposal\Estimator\Feature\EstimatedFeatureValue[]
     */
    protected $featureValues;

    /**
     * @var \Shop\DiscountBundle\Proposal\ActionCondition\ProposalActionConditions
     */
    protected $actionConditions;

    function __construct($proposal, $price)
    {
        $this->proposal = $proposal;
        $this->price = $price;
        $this->featureValues = [];
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
     * @return \Shop\CatalogBundle\Proposal\Estimator\Feature\Price\PriceFeatureValue
     */
    public function getPriceFeatureValue()
    {
        return $this->priceFeatureValue;
    }

    /**
     * @param \Shop\CatalogBundle\Proposal\Estimator\Feature\Price\PriceFeatureValue $priceFeatureValue
     * @return $this
     */
    public function setPriceFeatureValue($priceFeatureValue)
    {
        $this->priceFeatureValue = $priceFeatureValue;
        return $this;
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

        if($this->getRateFeatureValue()){

            $rate = $this->getRateFeatureValue()->getValue();
            $rate++;
            $this->getRateFeatureValue()
                ->setPriority($rate)
                ->setValue($rate)
            ;

        }

        return $this;

    }

    /**
     * @return int
     */
    public function getRate()
    {

        $rate = 0;

        if($this->getRateFeatureValue()){
            $rate = $this->getRateFeatureValue()->getValue();
        }

        return $rate;

    }

    /**
     * @return EstimatedFeatureValue
     */
    public function getRateFeatureValue()
    {
        return $this->rateFeatureValue;
    }

    /**
     * @param EstimatedFeatureValue $rateFeatureValue
     * @return $this
     */
    public function setRateFeatureValue($rateFeatureValue)
    {
        $this->rateFeatureValue = $rateFeatureValue;
        return $this;
    }

    /**
     * @return \Shop\DiscountBundle\Proposal\ActionCondition\ProposalActionConditions
     */
    public function getActionConditions()
    {
        return $this->actionConditions;
    }

    /**
     * @param \Shop\DiscountBundle\Proposal\ActionCondition\ProposalActionConditions $actionConditions
     * @return $this
     */
    public function setActionConditions($actionConditions)
    {
        $this->actionConditions = $actionConditions;
        return $this;
    }

} 