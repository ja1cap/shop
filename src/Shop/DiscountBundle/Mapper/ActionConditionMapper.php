<?php
namespace Shop\DiscountBundle\Mapper;

use Doctrine\Common\Collections\Collection;
use Shop\DiscountBundle\Entity\ActionConditionCategory;
use Shop\DiscountBundle\Entity\ActionConditionGiftProposal;
use Shop\DiscountBundle\Entity\ActionConditionProposal;
use Weasty\Doctrine\Mapper\AbstractEntityMapper;
use Weasty\Doctrine\Mapper\EntityCollectionMapper;

/**
 * Class ActionConditionMapper
 * @package Shop\DiscountBundle\Mapper
 */
class ActionConditionMapper extends AbstractEntityMapper {

    /**
     * @param $categoryIds
     * @return $this
     */
    public function setCategoryIds($categoryIds){

        $em = $this->getEntityManager();

        /**
         * @var $condition \Shop\DiscountBundle\Entity\ActionCondition
         */
        $condition = $this->getEntity();

        $data = array();

        if(is_array($categoryIds) || $categoryIds instanceof Collection){
            foreach($categoryIds as $categoryId){
                if($categoryId){
                    $data[] = array(
                        'category_id' => $categoryId,
                        'condition' => $condition,
                    );
                }
            }
        }

        $categoryCollectionMapper = new EntityCollectionMapper(
            $condition->getCategories(),
            function(){
                return new ActionConditionCategory();
            },
            array($condition, 'addCategory'),
            function(ActionConditionCategory $actionConditionCategory) use ($condition, $em) {
                $condition->removeCategory($actionConditionCategory);
                $em->remove($actionConditionCategory);
            }
        );

        $categoryCollectionMapper->map($data, 'category_id');

        return $this;

    }

    /**
     * @param $proposalIds
     * @return $this
     */
    public function setProposalIds($proposalIds){

        $em = $this->getEntityManager();

        /**
         * @var $condition \Shop\DiscountBundle\Entity\ActionCondition
         */
        $condition = $this->getEntity();

        $data = array();

        if(is_array($proposalIds) || $proposalIds instanceof Collection){
            foreach($proposalIds as $proposalId){
                if($proposalId){
                    $data[] = array(
                        'proposal_id' => $proposalId,
                        'condition' => $condition,
                    );
                }
            }
        }

        $proposalCollectionMapper = new EntityCollectionMapper(
            $condition->getProposals(),
            function(){
                return new ActionConditionProposal();
            },
            array($condition, 'addProposal'),
            function(ActionConditionProposal $actionConditionProposal) use ($condition, $em) {
                $condition->removeProposal($actionConditionProposal);
                $em->remove($actionConditionProposal);
            }
        );

        $proposalCollectionMapper->map($data, 'proposal_id');

        return $this;

    }

    /**
     * @param $proposalIds
     * @return $this
     */
    public function setGiftProposalIds($proposalIds){

        $em = $this->getEntityManager();

        /**
         * @var $condition \Shop\DiscountBundle\Entity\ActionCondition
         */
        $condition = $this->getEntity();

        $data = array();

        if(is_array($proposalIds) || $proposalIds instanceof Collection){
            foreach($proposalIds as $proposalId){
                if($proposalId){
                    $data[] = array(
                        'proposal_id' => $proposalId,
                        'condition' => $condition,
                    );
                }
            }
        }

        $proposalCollection = $condition->getGifts();
        $proposalCollectionMapper = new EntityCollectionMapper(
            $proposalCollection,
            function(){
                return new ActionConditionGiftProposal();
            },
            function(ActionConditionGiftProposal $actionConditionDiscountProposal) use($condition) {
                return $condition->addGift($actionConditionDiscountProposal);
            },
            function(ActionConditionGiftProposal $actionConditionDiscountProposal) use ($condition, $em) {
                $condition->removeGift($actionConditionDiscountProposal);
                $em->remove($actionConditionDiscountProposal);
            }
        );

        $proposalCollectionMapper->map($data, 'proposal_id');

        return $this;

    }

}