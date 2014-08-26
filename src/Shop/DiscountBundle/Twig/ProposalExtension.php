<?php
namespace Shop\DiscountBundle\Twig;

/**
 * Class ProposalExtension
 * @package Shop\DiscountBundle\Twig
 */
class ProposalExtension extends \Twig_Extension {

    /**
     * @var \Shop\DiscountBundle\Proposal\ActionCondition\ProposalActionConditionsBuilder
     */
    protected $proposalActionConditionsBuilder;

    function __construct($proposalActionConditionsBuilder)
    {
        $this->proposalActionConditionsBuilder = $proposalActionConditionsBuilder;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('shop_discount_proposal_action_conditions', array($this, 'buildProposalActionConditions')),
        );
    }

    /**
     * @param $proposal
     * @param $actionConditionIds
     * @return \Shop\DiscountBundle\Proposal\ActionCondition\ProposalActionConditions
     */
    public function buildProposalActionConditions($proposal, $actionConditionIds){
        return $this->proposalActionConditionsBuilder->build($proposal, $actionConditionIds);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'shop_discount_proposal';
    }

} 