<?php
namespace Shop\ShippingBundle\Entity;

use Weasty\Doctrine\Entity\AbstractRepository;

/**
 * Class ShippingMethodCategoryRepository
 * @package Shop\ShippingBundle\Entity
 */
class ShippingMethodCategoryRepository extends AbstractRepository {

    /**
     * @param $shippingMethodId
     * @param $categoryId
     * @return null|\Shop\ShippingBundle\Entity\ShippingMethodCategory
     */
    public function getShippingMethodCategory($shippingMethodId, $categoryId){

        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb
            ->select(array(
                'smc.id'
            ))
            ->from('ShopShippingBundle:ShippingMethodCategory', 'smc')
            ->andWhere($qb->expr()->andX(
                $qb->expr()->eq('smc.shippingMethodId', $shippingMethodId),
                'FIND_IN_SET(' . (int)$categoryId . ', smc.categoryIds)'
            ))
        ;

        $this->convertDqlToSql($qb);
        $sql = (string)$qb;

        $shippingMethodId = $this->getEntityManager()->getConnection()->executeQuery($sql)->fetchColumn();
        $shippingMethodCategory = $shippingMethodId ? $this->findOneBy(array('id' => $shippingMethodId)) : null;

        return $shippingMethodCategory;


    }

} 