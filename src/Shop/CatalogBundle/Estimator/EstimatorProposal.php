<?php
namespace Shop\CatalogBundle\Estimator;

/**
 * Class EstimatorProposal
 * @package Shop\CatalogBundle\Estimator
 */
class EstimatorProposal {

    /**
     * @var \Shop\CatalogBundle\Proposal\ProposalInterface
     */
    protected $proposal;

    /**
     * @var \Shop\CatalogBundle\Price\ProposalPriceInterface
     */
    protected $price;

    /**
     * @var \Shop\DiscountBundle\Proposal\ActionCondition\ProposalActionConditions
     */
    protected $actionConditions;

    function __construct($proposal, $price)
    {
        $this->proposal = $proposal;
        $this->price = $price;
        $this->actionConditionIds = [];
    }

    /**
     * @return \Shop\CatalogBundle\Proposal\ProposalInterface
     */
    public function getProposal()
    {
        return $this->proposal;
    }

    /**
     * @return \Shop\CatalogBundle\Price\ProposalPriceInterface
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return null|\Shop\DiscountBundle\Price\DiscountPrice
     */
    public function getDiscountPrice(){
        return (($this->getPrice() && $this->getActionConditions()) ? $this->getActionConditions()->getDiscountPrice($this->getPrice()) : null);
    }

    /**
     * @return int[]
     */
    public function getActionConditionIds()
    {
        return $this->getActionConditions() ? $this->getActionConditions()->getConditionIds() : [];
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