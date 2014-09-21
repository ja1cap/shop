<?php
namespace Shop\DiscountBundle\Mapper;

use Doctrine\Common\Collections\Collection;
use Shop\CatalogBundle\Entity\Proposal;
use Shop\DiscountBundle\Entity\AbstractActionConditionGift;
use Weasty\Doctrine\Mapper\AbstractEntityMapper;
use Weasty\Doctrine\Mapper\EntityCollectionMapper;

/**
 * Class ActionConditionMapper
 * @package Shop\DiscountBundle\Mapper
 */
class ActionConditionMapper extends AbstractEntityMapper {

    /**
     * @param $proposals
     * @return $this
     */
    public function setGiftProposals($proposals){

        $em = $this->getEntityManager();

        /**
         * @var $condition \Shop\DiscountBundle\Entity\AbstractActionCondition
         */
        $condition = $this->getEntity();

        $data = array();

        if(is_array($proposals) || $proposals instanceof Collection){
            foreach($proposals as $proposal){
                if($proposal instanceof Proposal){
                    $data[] = array(
                        'proposal' => $proposal,
                        'proposal_id' => $proposal->getId(),
                        'condition' => $condition,
                    );
                }
            }
        }

        $giftCollection = $condition->getGifts();
        $giftCollectionMapper = new EntityCollectionMapper(
            $giftCollection,
            function() use ($condition) {
                return $condition->createGift();
            },
            function(AbstractActionConditionGift $gift) use($condition) {
                return $condition->addGift($gift);
            },
            function(AbstractActionConditionGift $gift) use ($condition, $em) {
                $condition->removeGift($gift);
                $em->remove($gift);
            }
        );

        $giftCollectionMapper->map($data, 'proposal_id');

        return $this;

    }

}