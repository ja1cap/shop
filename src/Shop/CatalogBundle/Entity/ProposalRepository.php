<?php
namespace Shop\CatalogBundle\Entity;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\EntityRepository;

/**
 * Class ProposalRepository
 * @package Shop\CatalogBundle\Entity
 */
class ProposalRepository extends EntityRepository {

    public function findProposalPrices($proposalId, $parameters){

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select('pp')
            ->from('ShopCatalogBundle:Proposal', 'p')
            ->join('ShopCatalogBundle:Price', 'pp', Expr\Join::WITH, $qb->expr()->eq('pp.proposalId', 'p.id'))
            ->leftJoin('ShopCatalogBundle:ParameterValue', 'pv', Expr\Join::WITH, $qb->expr()->eq('pv.priceId', 'pp.id'));

        $qb->andWhere($qb->expr()->eq('p.id', $proposalId));

        if(is_array($parameters)){

            $parametersExpr = array();

            foreach(array_filter($parameters) as $parameterId => $optionId){

                if($optionId){

                    $parametersExpr[] = $qb->expr()->andX(
                        $qb->expr()->eq('pv.parameterId', $parameterId),
                        $qb->expr()->eq('pv.optionId', $optionId)
                    );

                }

            }

            if($parametersExpr){
                $qb->andWhere(call_user_func_array(array($qb->expr(), 'orX'), $parametersExpr));
            }

        }

        $qb->addOrderBy('pp.value', 'ASC');

//        var_dump($qb->getDQL());

        return $qb->getQuery()->getResult();

    }

    public function findProposals($categoryId){

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select(array(
                'p AS proposal',
                'MIN(pp.value) AS price'
            ))
            ->from('ShopCatalogBundle:Proposal', 'p')
            ->join('ShopCatalogBundle:Price', 'pp', Expr\Join::WITH, $qb->expr()->eq('pp.proposalId', 'p.id'));

        $qb->where($qb->expr()->eq('p.categoryId', $categoryId));


        $qb->addOrderBy('price', 'ASC');
        $qb->addGroupBy('p.id');

//        var_dump($qb->getDQL());

        return $qb->getQuery()->getResult();

    }

} 