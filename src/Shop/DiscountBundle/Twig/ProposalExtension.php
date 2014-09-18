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
        return [];
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