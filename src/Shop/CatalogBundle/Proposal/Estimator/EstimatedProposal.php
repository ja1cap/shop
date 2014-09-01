<?php
namespace Shop\CatalogBundle\Proposal\Estimator;

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
     * @var \Weasty\Bundle\CatalogBundle\Feature\FeaturesResourceInterface
     */
    protected $features;

    function __construct($proposal, $price, $features)
    {
        $this->proposal = $proposal;
        $this->price = $price;
        $this->features = $features;
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
     * @return \Weasty\Bundle\CatalogBundle\Feature\FeaturesResourceInterface
     */
    public function getFeatures()
    {
        return $this->features;
    }

} 