<?php
namespace Weasty\Bundle\AdBundle\Entity;

use Weasty\Doctrine\Entity\AbstractRepository;

/**
 * Class BaseBannerRepository
 * @package Weasty\Bundle\AdBundle\Entity
 */
class BaseBannerRepository extends AbstractRepository {

    /**
     * @return \Weasty\Bundle\AdBundle\Banner\BannerInterface[]
     */
    public function getBanners(){
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select('b')
            ->from('WeastyAdBundle:BaseBanner', 'b')
            ->orderBy('b.id', 'DESC')
        ;
        return $qb->getQuery()->getResult();
    }

}