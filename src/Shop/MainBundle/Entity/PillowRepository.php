<?php
namespace Shop\MainBundle\Entity;

use Doctrine\ORM\Query\Expr;

/**
 * Class PillowRepository
 * @package Shop\MainBundle\Entity
 */
class PillowRepository extends ProposalRepository {

    public function getPillows(){

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select(array(
                'p AS proposal',
                'MIN(pp.value) AS price'
            ))
            ->from('ShopMainBundle:Pillow', 'p')
            ->join('ShopMainBundle:PillowPrice', 'pp', Expr\Join::WITH, $qb->expr()->eq('pp.proposalId', 'p.id'));

        $qb->addOrderBy('price', 'ASC');
        $qb->addGroupBy('p.id');

//        var_dump($qb->getDQL());

        return $qb->getQuery()->getResult();

    }

} 