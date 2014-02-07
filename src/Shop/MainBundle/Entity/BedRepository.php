<?php
namespace Shop\MainBundle\Entity;

use Doctrine\ORM\Query\Expr;

/**
 * Class BedRepository
 * @package Shop\MainBundle\Entity
 */
class BedRepository extends ProposalRepository {

    public function getBeds($sizeId = null){

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select(array(
                'b AS proposal',
                'MIN(bp.value) AS price'
            ))
            ->from('ShopMainBundle:Bed', 'b')
            ->join('ShopMainBundle:BedPrice', 'bp', Expr\Join::WITH, $qb->expr()->eq('bp.proposalId', 'b.id'));

        if($sizeId){
            $qb->andWhere($qb->expr()->eq('bp.sizeId', (int)$sizeId));
        }

        $qb->addOrderBy('price', 'ASC');
        $qb->addGroupBy('b.id');

//        var_dump($qb->getDQL());

        return $qb->getQuery()->getResult();

    }

} 