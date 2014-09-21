<?php
namespace Shop\DiscountBundle\Proposal;

use Shop\DiscountBundle\ActionCondition\ActionConditionInterface;

/**
 * Interface ActionProposalInterface
 * @package Shop\DiscountBundle\Proposal
 */
interface ActionProposalInterface extends ActionConditionInterface {

    /**
     * @return int
     */
    public function getProposalId();

} 