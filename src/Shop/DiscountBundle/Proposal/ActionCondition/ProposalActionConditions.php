<?php
namespace Shop\DiscountBundle\Proposal\ActionCondition;

use Shop\DiscountBundle\ActionCondition\ActionConditionInterface;
use Shop\DiscountBundle\Proposal\DiscountPrice\ProposalDiscountPriceCalculator;
use Weasty\Bundle\CatalogBundle\Proposal\ProposalInterface;

/**
 * Class ProposalActionConditions
 * @package Shop\DiscountBundle\Proposal\ActionCondition
 */
class ProposalActionConditions {

    /**
     * @var \Shop\DiscountBundle\ActionCondition\ActionConditionInterface[]
     */
    protected $conditions;

    /**
     * @var \Shop\DiscountBundle\ActionCondition\ActionConditionInterface
     */
    protected $mainCondition;

    /**
     * @var \Shop\DiscountBundle\ActionCondition\ActionConditionInterface[]
     */
    protected $complexConditions;

    /**
     * @var \Shop\DiscountBundle\ActionCondition\ActionConditionInterface[]
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
     * @param \Shop\DiscountBundle\ActionCondition\ActionConditionInterface[] $conditions
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
     * @return \Shop\DiscountBundle\ActionCondition\ActionConditionInterface[]
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
     * @return null|\Shop\DiscountBundle\ActionCondition\ActionConditionInterface
     */
    public function getMainCondition(){

        if($this->mainCondition === null && $this->conditions){

            reset($this->conditions);
            $this->mainCondition = current($this->conditions);

            if($this->mainCondition && $this->mainCondition->getType() == ActionConditionInterface::TYPE_INHERIT){

                $this->mainCondition = ($this->mainCondition->getParentCondition() ?: $this->mainCondition);

            }

        }

        return $this->mainCondition;

    }

    /**
     * @return \Shop\DiscountBundle\ActionCondition\ActionConditionInterface[]
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
     * @return \Shop\DiscountBundle\ActionCondition\ActionConditionInterface[]
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
     * @param ActionConditionInterface $condition
     * @return bool
     */
    public function isGiftCondition(ActionConditionInterface $condition = null){

        if(!$condition){
            return false;
        }

        if($condition->getType() == $condition::TYPE_INHERIT){

            $parentCondition = $condition->getParentCondition();
            $result = ($parentCondition ? $parentCondition->getIsGiftCondition() : false);

        } else {

            $result = $condition->getIsGiftCondition();

        }

        return $result;

    }

    /**
     * @return \Shop\DiscountBundle\ActionCondition\ActionConditionInterface[]
     */
    public function getGiftConditions()
    {
        return array_filter($this->conditions, [$this, 'isGiftCondition']);
    }

    /**
     * @return null|ActionConditionInterface
     */
    public function getGiftCondition(){

        $giftCondition = null;

        if($this->isGiftCondition($this->getMainCondition())){
            $giftCondition = $this->getMainCondition();
        }

        return $giftCondition;

    }

    /**
     * @param null $giftCondition
     * @return null|\Shop\CatalogBundle\Proposal\ProposalInterface[]
     */
    public function getGifts($giftCondition = null){

        $giftCondition = $giftCondition ?: $this->getGiftCondition();
        $giftProposals = $giftCondition && $giftCondition->getIsGiftCondition() ? $giftCondition->getGiftProposals() : null;

        return $giftProposals;

    }

    /**
     * @param ActionConditionInterface $condition
     * @return bool
     */
    public function isDiscountCondition(ActionConditionInterface $condition = null){

        if(!$condition){
            return false;
        }

        if($condition->getType() == $condition::TYPE_INHERIT){

            $parentCondition = $condition->getParentCondition();
            $result = ($parentCondition ? $parentCondition->getIsDiscountCondition() : false);

        } else {

            $result = $condition->getIsDiscountCondition();

        }

        return $result;

    }

    /**
     * @return \Shop\DiscountBundle\ActionCondition\ActionConditionInterface[]
     */
    public function getDiscountConditions()
    {
        return array_filter($this->conditions, [$this, 'isDiscountCondition']);
    }

    /**
     * @return null|ActionConditionInterface
     */
    public function getDiscountCondition(){

        $discountCondition = null;

        if($this->isDiscountCondition($this->getMainCondition())){
            $discountCondition = $this->getMainCondition();
        }

        return $discountCondition;

    }

    /**
     * @param $proposalPrice
     * @param \Shop\DiscountBundle\ActionCondition\ActionConditionInterface $discountCondition
     * @return null|\Shop\DiscountBundle\Price\DiscountPrice
     */
    public function getDiscountPrice($proposalPrice, $discountCondition = null){

        $discountCondition = $discountCondition ?: $this->getDiscountCondition();
        return $discountCondition ? $this->discountPriceCalculator->calculate($proposalPrice, $discountCondition) : null;

    }

} 