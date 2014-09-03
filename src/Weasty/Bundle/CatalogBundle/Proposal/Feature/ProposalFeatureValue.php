<?php
namespace Weasty\Bundle\CatalogBundle\Proposal\Feature;

use Weasty\Bundle\CatalogBundle\Feature\FeatureValue;

/**
 * Class ProposalFeatureValue
 * @package Weasty\Bundle\CatalogBundle\Proposal\Feature
 */
class ProposalFeatureValue extends FeatureValue
    implements ProposalFeatureValueInterface
{

    /**
     * @var int
     */
    protected $priceId;

    /**
     * @var int
     */
    protected $proposalId;

    /**
     * @return int
     */
    public function getPriceId()
    {
        return $this->priceId;
    }

    /**
     * @param int $priceId
     * @return $this
     */
    public function setPriceId($priceId)
    {
        $this->priceId = $priceId;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getProposalId()
    {
        return $this->proposalId;
    }

    /**
     * @param int $proposalId
     * @return $this
     */
    public function setProposalId($proposalId)
    {
        $this->proposalId = $proposalId;
        return $this;
    }

} 