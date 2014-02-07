<?php
namespace Shop\MainBundle\Entity;

use Doctrine\ORM\Query\Expr;

/**
 * Class MattressRepository
 * @package Shop\MainBundle\Entity
 */
class MattressRepository extends ProposalRepository {

    public function getMattresses($sizeId = null, $manufacturerId = null){

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select(array(
            'm AS proposal',
            'MIN(mp.value) AS price'
        ))
            ->from('ShopMainBundle:Mattress', 'm')
            ->join('ShopMainBundle:MattressPrice', 'mp', Expr\Join::WITH, $qb->expr()->eq('mp.proposalId', 'm.id'));

        if($manufacturerId){
            $qb->andWhere($qb->expr()->eq('m.manufacturerId', (int)$manufacturerId));
        }

        if($sizeId){
            $qb->andWhere($qb->expr()->eq('mp.sizeId', (int)$sizeId));
        }

        $qb->addOrderBy('price', 'ASC');
        $qb->addGroupBy('m.id');

//        var_dump($qb->getDQL());

        return $qb->getQuery()->getResult();

    }

} 