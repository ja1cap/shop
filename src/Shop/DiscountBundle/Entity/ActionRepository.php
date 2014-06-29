<?php
namespace Shop\DiscountBundle\Entity;

use Weasty\Doctrine\Entity\AbstractRepository;
use Doctrine\ORM\Query\Expr;

/**
 * Class ActionRepository
 * @package Shop\DiscountBundle\Entity
 */
class ActionRepository extends AbstractRepository {

    public function findActions($proposalId){

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select(array(
                'ac.id',
            ))
            ->from('ShopCatalogBundle:Proposal', 'p')
            ->join('ShopCatalogBundle:Category', 'c', Expr\Join::WITH, $qb->expr()->eq('c.id', 'p.categoryId'))
            ->leftJoin('ActionConditionProposal', 'acp', Expr\Join::LEFT_JOIN, $qb->expr()->eq('acp.proposal_id', 'p.id'))
            ->leftJoin('ActionConditionCategory', 'acc', Expr\Join::LEFT_JOIN, $qb->expr()->eq('acc.category_id', 'c.id'))
            ->leftJoin('ShopDiscountBundle:ActionCondition', 'ac', Expr\Join::LEFT_JOIN, $qb->expr()->orX(
                $qb->expr()->eq('ac.id', 'acc.condition_id'),
                $qb->expr()->eq('ac.id', 'acp.condition_id')
            ))
        ;

        $qb
            ->andWhere($qb->expr()->eq('p.id', (int)$proposalId))
        ;

        $this->convertDqlToSql($qb);
        $sql = (string)$qb;
        $ids = $this->getEntityManager()->getConnection()->executeQuery($sql)->fetchAll(\PDO::FETCH_COLUMN);

        /**
         * @var $conditions \Shop\DiscountBundle\Entity\ActionCondition[]
         */
        $conditions = $this->getEntityManager()->getRepository('ShopDiscountBundle:ActionCondition')->findBy(
            array(
                'id' => $ids,
            ),
            array(
                'priority' => 'DESC',
            )
        );

        $actionsData = array();
        foreach($conditions as $condition){

            if(!isset($actionsData[$condition->getActionId()])){

                $actionsData[$condition->getActionId()] = array(
                    'action' => $condition->getAction(),
                    'finalConditions' => array(),
                    'complexConditions' => array(),
                );

            }

            if($condition->getIsComplex()){
                $actionsData[$condition->getActionId()]['complexConditions'][] = $condition;
            } else {
                $actionsData[$condition->getActionId()]['finalConditions'][] = $condition;
            }

        }

        return $actionsData;

    }

} 