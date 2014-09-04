<?php
namespace Shop\CatalogBundle\Proposal\Estimator;

/**
 * Class EstimatorProposal
 * @package Shop\CatalogBundle\Proposal\Estimator
 */
class EstimatorProposal {

    /**
     * @var \Shop\CatalogBundle\Proposal\ProposalInterface
     */
    protected $proposal;

    /**
     * @var \Shop\CatalogBundle\Proposal\Price\ProposalPriceInterface
     */
    protected $price;

    /**
     * @var int[]
     */
    protected $actionConditionIds;

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
     * @return \Shop\CatalogBundle\Proposal\Price\ProposalPriceInterface
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return int[]
     */
    public function getActionConditionIds()
    {
        return $this->actionConditionIds;
    }

    /**
     * @param int[] $actionConditionIds
     * @return $this
     */
    public function setActionConditionIds($actionConditionIds)
    {
        $this->actionConditionIds = $actionConditionIds;
        return $this;
    }

}