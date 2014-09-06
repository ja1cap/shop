<?php
namespace Shop\DiscountBundle\Proposal\ActionCondition;

use Shop\DiscountBundle\Entity\ActionConditionInterface;
use Shop\DiscountBundle\Proposal\DiscountPrice\ProposalDiscountPriceCalculator;
use Weasty\Bundle\CatalogBundle\Proposal\ProposalInterface;

/**
 * Class ProposalActionConditions
 * @package Shop\DiscountBundle\Proposal\ActionCondition
 */
class ProposalActionConditions {

    /**
     * @var \Shop\DiscountBundle\Entity\ActionConditionInterface[]
     */
    protected $conditions;

    /**
     * @var \Shop\DiscountBundle\Entity\ActionConditionInterface
     */
    protected $mainCondition;

    /**
     * @var \Shop\DiscountBundle\Entity\ActionConditionInterface[]
     */
    protected $complexConditions;

    /**
     * @var \Shop\DiscountBundle\Entity\ActionConditionInterface[]
     */
    protected $singleConditions;

    /**
     * @var \Shop\DiscountBundle\Proposal\DiscountPrice\ProposalDiscountPriceCalculator
     */
    protected $discountPriceCalculator;

    /**
     * @var \Weasty\Bundle\CatalogBundle\Proposal\ProposalInterface
     */
    protected $proposal;

    function __construct(ProposalDiscountPriceCalculator $discountPriceCalculator, ProposalInterface $proposal, $conditions = [])
    {
        $this->discountPriceCalculator = $discountPriceCalculator;
        $this->proposal = $proposal;
        $this->conditions = $conditions;
    }

    /**
     * @return $this
     */
    protected function _resetConditionGroups(){
        $this->mainCondition = null;
        $this->singleConditions = null;
        $this->complexConditions = null;
        return $this;
    }

    /**
     * @param \Shop\DiscountBundle\Entity\ActionConditionInterface[] $conditions
     * @param boolean $resetConditionGroups
     * @return $this
     */
    public function setConditions($conditions, $resetConditionGroups = false)
    {
        $this->conditions = $conditions;
        if($resetConditionGroups){
            $this->_resetConditionGroups();
        }
        return $this;
    }

    /**
     * @return \Shop\DiscountBundle\Entity\ActionConditionInterface[]
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * @return array
     */
    public function getConditionIds(){
        return array_map(function(ActionConditionInterface $actionCondition){
            return $actionCondition->getId();
        }, $this->getConditions());
    }

    /**
     * @return null|ActionConditionInterface
     */
    public function getMainCondition(){

        if($this->mainCondition === null && $this->conditions){

            reset($this->conditions);
            $this->mainCondition = current($this->conditions);

        }

        return $this->mainCondition;

    }

    /**
     * @return \Shop\DiscountBundle\Entity\ActionConditionInterface[]
     */
    public function getSingleConditions()
    {
        if($this->singleConditions === null && $this->conditions){
            $this->singleConditions = array_filter($this->conditions, function(ActionConditionInterface $condition){
                return !$condition->getIsComplex();
            });
        }
        return $this->singleConditions;
    }

    /**
     * @return \Shop\DiscountBundle\Entity\ActionConditionInterface[]
     */
    public function getComplexConditions()
    {
        if($this->complexConditions === null && $this->conditions){
            $this->complexConditions = array_filter($this->conditions, function(ActionConditionInterface $condition){
                return $condition->getIsComplex();
            });
        }
        return $this->complexConditions;
    }

    /**
     * @param \Shop\DiscountBundle\Entity\ActionConditionInterface[] $conditions
     * @return \Shop\DiscountBundle\Entity\ActionConditionInterface[]
     */
    public function getDiscountConditions($conditions = [])
    {

        if(!$conditions){
            $conditions = $this->conditions;
        }

        return array_filter($conditions, function(ActionConditionInterface $condition){
            return $condition->getIsPriceDiscount();
        });
    }

    /**
     * @param $proposalPrice
     * @return null|\Shop\DiscountBundle\Price\DiscountPrice
     */
    public function getDiscountPrice($proposalPrice){
        return $this->discountPriceCalculator->calculate($proposalPrice, $this->getDiscountConditions());
    }

} 