<?php

namespace Shop\CatalogBundle\Entity;

use Doctrine\ORM\Query\Expr;
use Weasty\DoctrineBundle\Entity\AbstractRepository;

/**
 * Class PopularProposalRepository
 * @package Shop\CatalogBundle\Entity
 */
class PopularProposalRepository extends AbstractRepository
{

    /**
     * @return array
     */
    public function findProposals(){

        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb
            ->select(array(
                'pp.id AS id',
                'pp.position AS position',
                'p AS proposal',
            ))
            ->from('ShopCatalogBundle:PopularProposal', 'pp')
            ->join('ShopCatalogBundle:Proposal', 'p', Expr\Join::WITH, $qb->expr()->eq('p.id', 'pp.proposalId'))
            ->orderBy('pp.position', 'ASC')
        ;

        return $qb->getQuery()->getResult();

    }

}
