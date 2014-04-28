<?php
namespace Shop\CatalogBundle\Entity;

use Weasty\DoctrineBundle\Entity\AbstractRepository;

/**
 * Class PriceRepository
 * @package Shop\CatalogBundle\Entity
 */
class PriceRepository extends AbstractRepository {

    /**
     * @param $proposalId
     * @param array $sku
     * @param array $manufacturerSku
     * @return array
     */
    public function findProposalPricesBySku($proposalId, array $sku = array(), array $manufacturerSku){

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select('pp')
            ->from('ShopCatalogBundle:Price', 'pp')
            ->andWhere($qb->expr()->andX(
                $qb->expr()->eq('pp.proposalId', (int)$proposalId),
                $qb->expr()->orX(
                    ($sku ? $qb->expr()->in('pp.sku', $sku) : null),
                    ($manufacturerSku ? $qb->expr()->in('pp.manufacturerSku', $manufacturerSku) : null)
                )
            ));

        return $qb->getQuery()->getResult();

    }

} 