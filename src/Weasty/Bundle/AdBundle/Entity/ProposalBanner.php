<?php

namespace Weasty\Bundle\AdBundle\Entity;

use Weasty\Bundle\AdBundle\Banner\ProposalBannerInterface;
use Weasty\Bundle\CatalogBundle\Proposal\ProposalInterface;

/**
 * Class ProposalBanner
 * @package Weasty\Bundle\AdBundle\Entity
 */
class ProposalBanner extends AbstractBanner implements  ProposalBannerInterface
{

    /**
     * @var integer
     */
    private $proposalId;

    /**
     * @return int
     */
    public function getType()
    {
        return self::TYPE_PROPOSAL;
    }

    /**
     * Set proposalId
     *
     * @param integer $proposalId
     * @return ProposalBanner
     */
    public function setProposalId($proposalId)
    {
        $this->proposalId = $proposalId;

        return $this;
    }

    /**
     * Get proposalId
     *
     * @return integer 
     */
    public function getProposalId()
    {
        return $this->proposalId;
    }


    /**
     * @var \Shop\CatalogBundle\Entity\Proposal
     */
    private $proposal;


    /**
     * Set proposal
     *
     * @param \Weasty\Bundle\CatalogBundle\Proposal\ProposalInterface $proposal
     * @return ProposalBanner
     */
    public function setProposal(ProposalInterface $proposal = null)
    {
        $this->proposal = $proposal;

        return $this;
    }

    /**
     * Get proposal
     *
     * @return \Weasty\Bundle\CatalogBundle\Proposal\ProposalInterface
     */
    public function getProposal()
    {
        return $this->proposal;
    }
}
