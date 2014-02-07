<?php

namespace Shop\CatalogBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;

/**
 * CategoryParameterRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CategoryParameterRepository extends EntityRepository
{

    /**
     * @param $categoryId
     * @return array
     */
    public function findCategoryUnusedParameters($categoryId){

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('p')
            ->from('ShopCatalogBundle:Parameter', 'p')
            ->leftJoin('ShopCatalogBundle:CategoryParameter', 'cp', Expr\Join::WITH, $qb->expr()->andX(
                $qb->expr()->eq('cp.categoryId', (int)$categoryId),
                $qb->expr()->eq('cp.parameterId', 'p.id')
            ));

        $qb->where($qb->expr()->isNull('cp.id'));

        $qb->orderBy('p.name');

        return $qb->getQuery()->getResult();

    }

}
