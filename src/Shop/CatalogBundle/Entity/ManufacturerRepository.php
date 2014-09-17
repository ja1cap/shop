<?php

namespace Shop\CatalogBundle\Entity;

use Weasty\Doctrine\Entity\AbstractRepository;

/**
 * Class ManufacturerRepository
 * @package Shop\CatalogBundle\Entity
 */
class ManufacturerRepository extends AbstractRepository
{

    /**
     * @return \Shop\CatalogBundle\Entity\Manufacturer[]
     */
    public function getManufacturesWithImages(){

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select('m')
            ->from('ShopCatalogBundle:Manufacturer', 'm')
            ->andWhere($qb->expr()->andX(
                $qb->expr()->isNotNull('m.imageId')
            ))
            ->orderBy('m.name', 'ASC')
        ;

        return $qb->getQuery()->getResult();

    }

}
