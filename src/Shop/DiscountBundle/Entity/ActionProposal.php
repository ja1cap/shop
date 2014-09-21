<?php
namespace Shop\DiscountBundle\Entity;

use Shop\CatalogBundle\Entity\Proposal;
use Shop\DiscountBundle\Proposal\ActionProposalElement;
use Shop\DiscountBundle\Proposal\ActionProposalInterface;

/**
 * Class ActionProposal
 * @package Shop\DiscountBundle\Entity
 */
class ActionProposal extends ActionCondition
    implements ActionProposalInterface
{

    /**
     * @var integer
     */
    private $proposalId;

    /**
     * @var \Shop\CatalogBundle\Entity\Proposal
     */
    private $proposal;

    public function __construct()
    {
        parent::__construct();
        $this->priority = 3;
    }

    /**
     * @param $collection \Weasty\Doctrine\Cache\Collection\CacheCollection
     * @return \Weasty\Doctrine\Cache\Collection\CacheCollectionElementInterface
     */
    public function createCollectionElement($collection)
    {
        return new ActionProposalElement($collection, $this);
    }

    /**
     * Set proposalId
     *
     * @param integer $proposalId
     * @return ActionProposal
     */
    public function setProposalId($proposalId)
    {
        $this->proposalId = $proposalId;

        return $this;
    }

    /**
     * @return int
     */
    public function getProposalId()
    {
        return $this->proposalId;
    }

    /**
     * Set proposal
     *
     * @param \Shop\CatalogBundle\Entity\Proposal $proposal
     * @return ActionProposal
     */
    public function setProposal(Proposal $proposal = null)
    {
        $this->proposal = $proposal;

        return $this;
    }

    /**
     * Get proposal
     *
     * @return \Shop\CatalogBundle\Entity\Proposal 
     */
    public function getProposal()
    {
        return $this->proposal;
    }

}
