<?php
namespace Shop\DiscountBundle\Proposal;

use Shop\DiscountBundle\ActionCondition\ActionConditionElement;

/**
 * Class ActionProposalElement
 * @package Shop\DiscountBundle\Proposal
 */
class ActionProposalElement extends ActionConditionElement
    implements ActionProposalInterface
{

    /**
     * @return int
     */
    public function getProposalId()
    {
        return $this->data['proposalId'];
    }

} 