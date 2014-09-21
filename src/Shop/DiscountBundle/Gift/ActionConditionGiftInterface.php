<?php
namespace Shop\DiscountBundle\Gift;

/**
 * Interface ActionConditionGiftInterface
 * @package Shop\DiscountBundle\Gift
 */
interface ActionConditionGiftInterface {

    /**
     * Get id
     *
     * @return integer
     */
    public function getId();

    /**
     * Get proposalId
     *
     * @return integer
     */
    public function getProposalId();

    /**
     * Get proposal
     *
     * @return \Shop\CatalogBundle\Proposal\ProposalInterface
     */
    public function getProposal();

    /**
     * Get conditionId
     *
     * @return integer
     */
    public function getConditionId();

} 