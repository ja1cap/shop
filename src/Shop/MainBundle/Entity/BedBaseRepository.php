<?php
namespace Shop\MainBundle\Entity;

use Doctrine\ORM\Query\Expr;

/**
 * Class BedBaseRepository
 * @package Shop\MainBundle\Entity
 */
class BedBaseRepository extends ProposalRepository {

    public function getBases($sizeId = null, $manufacturerId = null){

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select(array(
            'bb AS proposal',
            'MIN(bbp.value) AS price'
        ))
            ->from('ShopMainBundle:BedBase', 'bb')
            ->join('ShopMainBundle:BedBasePrice', 'bbp', Expr\Join::WITH, $qb->expr()->eq('bbp.proposalId', 'bb.id'));

        if($manufacturerId){
            $qb->andWhere($qb->expr()->eq('bb.manufacturerId', (int)$manufacturerId));
        }

        if($sizeId){
            $qb->andWhere($qb->expr()->eq('bbp.sizeId', (int)$sizeId));
        }

        $qb->addOrderBy('price', 'ASC');
        $qb->addGroupBy('bb.id');

//        var_dump($qb->getDQL());

        return $qb->getQuery()->getResult();

    }

} 