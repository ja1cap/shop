<?php
namespace Shop\DiscountBundle\Proposal\ActionCondition;

use Shop\DiscountBundle\Category\ActionCategoryInterface;
use Shop\DiscountBundle\Proposal\ActionProposalInterface;
use Shop\DiscountBundle\Proposal\DiscountPrice\ProposalDiscountPriceCalculator;
use Shop\DiscountBundle\Action\ActionInterface;
use Shop\DiscountBundle\ActionCondition\ActionConditionInterface;
use Weasty\Bundle\CatalogBundle\Proposal\Price\ProposalPriceInterface;
use Weasty\Bundle\CatalogBundle\Proposal\ProposalInterface;
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
     * @param \Shop\DiscountBundle\ActionCondition\ActionConditionInterface $a
     * @param \Shop\DiscountBundle\ActionCondition\ActionConditionInterface $b
     * @return int
     */
    public function compareConditions(ActionConditionInterface $a, ActionConditionInterface $b){

        if($a->getActionId() != $b->getActionId()){
            return ($a->getAction()->getPosition() > $a->getAction()->getPosition() ? -1 : 1);
        }

        return ($a->getPriority() > $b->getPriority() ? -1 : 1);

    }

    /**
     * @param \Weasty\Bundle\CatalogBundle\Proposal\ProposalInterface $proposal
     * @param \Weasty\Bundle\CatalogBundle\Proposal\Price\ProposalPriceInterface $proposalPrice
     * @param array $actionConditionIds
     * @return \Shop\DiscountBundle\Proposal\ActionCondition\ProposalActionConditions
     */
    public function build($proposal, $proposalPrice, $actionConditionIds = []){

        $proposalActionConditions = new ProposalActionConditions($this->discountPriceCalculator, $proposal);

        if(!$actionConditionIds || !$proposal instanceof ProposalInterface || !$proposalPrice instanceof ProposalPriceInterface){
            return $proposalActionConditions;
        }

        $actionConditions = [];
        $actionConditionsAmount = 0;

        foreach($actionConditionIds as $actionConditionId){

            $isValid = false;
            $actionCondition = $this->actionConditionCollection->get($actionConditionId);

            if(
                $actionCondition instanceof ActionConditionInterface
                && $actionCondition->getAction()->getStatus() == ActionInterface::STATUS_ON
            ){

                if($actionCondition instanceof ActionCategoryInterface){

                    $isValid = ($proposal->getCategoryId() == $actionCondition->getCategoryId());

                } elseif($actionCondition instanceof ActionProposalInterface){

                    $isValid = ($proposal->getId() == $actionCondition->getProposalId());

                }

            }

            if($isValid){
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