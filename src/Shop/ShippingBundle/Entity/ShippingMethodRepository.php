<?php

namespace Shop\ShippingBundle\Entity;

use Doctrine\ORM\Query\Expr;
use Weasty\Doctrine\Entity\AbstractRepository;
use Weasty\Bundle\GeonamesBundle\Entity\City;

/**
 * Class ShippingMethodRepository
 * @package Shop\ShippingBundle\Entity
 */
class ShippingMethodRepository extends AbstractRepository
{

    /**
     * @param City $city
     * @return ShippingMethod|null
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getCityShippingMethods(City $city){

        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb
            ->select(array(
                'sm.id',
                'FIND_IN_SET(' . $city->getGeonameIdentifier() . ', smc.cityGeonameIds) AS hasCity',
                ($city->getState() ? 'FIND_IN_SET(' . $city->getState()->getGeonameIdentifier() . ', smc.stateGeonameIds) AS hasState' : null)
            ))
            ->from('ShopShippingBundle:ShippingMethod', 'sm')
            ->join('ShopShippingBundle:ShippingMethodCountry', 'smc', Expr\Join::WITH, $qb->expr()->eq('smc.shippingMethodId', 'sm.id'))
            ->andWhere($qb->expr()->orX(
                $qb->expr()->eq('smc.countryCode', $qb->expr()->literal($city->getCountry()->getCode())),
                $qb->expr()->in('smc.cityGeonameIds', $city->getGeonameIdentifier()),
                ($city->getState() ? $qb->expr()->in('smc.stateGeonameIds', $city->getState()->getGeonameIdentifier()) : null)
            ))
        ;

        $this->convertDqlToSql($qb);
        $sql = (string)$qb . ' ORDER BY hasState DESC, hasCity DESC LIMIT 1';

        $shippingMethodId = $this->getEntityManager()->getConnection()->executeQuery($sql)->fetchColumn();
        $shippingMethod = $shippingMethodId ? $this->findOneBy(array('id' => $shippingMethodId)) : null;

        return $shippingMethod;

    }

}
