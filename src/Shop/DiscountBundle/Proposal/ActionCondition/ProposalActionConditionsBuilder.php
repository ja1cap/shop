<?php
namespace Shop\DiscountBundle\Proposal\ActionCondition;

use Shop\DiscountBundle\Proposal\DiscountPrice\ProposalDiscountPriceCalculator;
use Shop\DiscountBundle\Entity\ActionInterface;
use Shop\DiscountBundle\Entity\ActionConditionInterface;
use Weasty\Bundle\CatalogBundle\Data\ProposalInterface;
use Weasty\Doctrine\Cache\Collection\CacheCollection;

/**
 * Class ProposalActionConditionsBuilder
 * @package Shop\DiscountBundle\Proposal\ActionCondition
 */
class ProposalActionConditionsBuilder {

    /**
     * @var \Weasty\Doctrine\Cache\Collection\CacheCollection
     */
    protected $actionConditionCollection;

    /**
     * @var \Shop\DiscountBundle\Proposal\DiscountPrice\ProposalDiscountPriceCalculator
     */
    protected $discountPriceCalculator;

    function __construct(CacheCollection $actionConditionCollection, ProposalDiscountPriceCalculator $discountPriceCalculator)
    {
        $this->actionConditionCollection = $actionConditionCollection;
        $this->discountPriceCalculator = $discountPriceCalculator;
    }

    /**
     * @param ActionConditionInterface $a
     * @param ActionConditionInterface $b
     * @return int
     */
    public function compareConditions(ActionConditionInterface $a, ActionConditionInterface $b){

        if($a->getActionId() != $b->getActionId()){
            return ($a->getAction()->getPosition() > $a->getAction()->getPosition() ? -1 : 1);
        }

        return ($a->getPriority() > $b->getPriority() ? -1 : 1);

    }

    /**
     * @param \Weasty\Bundle\CatalogBundle\Data\ProposalInterface $proposal
     * @param array $actionConditionIds
     * @return \Shop\DiscountBundle\Proposal\ActionCondition\ProposalActionConditions
     */
    public function build($proposal, $actionConditionIds = []){

        $proposalActionConditions = new ProposalActionConditions($this->discountPriceCalculator, $proposal);

        if(!$actionConditionIds || !$proposal instanceof ProposalInterface){
            return $proposalActionConditions;
        }

        $actionConditions = [];
        $actionConditionsAmount = 0;

        foreach($actionConditionIds as $actionConditionId){

            $actionCondition = $this->actionConditionCollection->get($actionConditionId);
            if(
                $actionCondition instanceof ActionConditionInterface
                && $actionCondition->getAction()->getStatus() == ActionInterface::STATUS_ON
            ){
                $actionConditions[] = $actionCondition;
                $actionConditionsAmount++;
            }

        }

        if($actionConditionsAmount > 1){

            usort($actionConditions, array($this, 'compareConditions'));

        }

        $proposalActionConditions->setConditions($actionConditions);

        return $proposalActionConditions;

    }

} 