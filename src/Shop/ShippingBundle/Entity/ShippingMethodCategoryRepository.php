<?php
namespace Shop\ShippingBundle\Entity;

use Weasty\DoctrineBundle\Entity\AbstractRepository;

/**
 * Class ShippingMethodCategoryRepository
 * @package Shop\ShippingBundle\Entity
 */
class ShippingMethodCategoryRepository extends AbstractRepository {

    public function getShippingMethodCategory($shippingMethodCategoryId, $categoryId){

        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb
            ->select(array(
                'smc.id'
            ))
            ->from('ShopShippingBundle:ShippingMethodCategory', 'smc')
            ->andWhere($qb->expr()->andX(
                $qb->expr()->eq('smc.shippingMethodId', $shippingMethodCategoryId),
                'FIND_IN_SET(' . (int)$categoryId . ', smc.categoryIds)'
            ))
        ;

        $this->convertDqlToSql($qb);
        $sql = (string)$qb;

        $shippingMethodCategoryId = $this->getEntityManager()->getConnection()->executeQuery($sql)->fetchColumn();
        $shippingMethodCategory = $shippingMethodCategoryId ? $this->findOneBy(array('id' => $shippingMethodCategoryId)) : null;

        return $shippingMethodCategory;


    }

} 