<?php
namespace Shop\CatalogBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

/**
 * Class ActionRepository
 * @package Shop\CatalogBundle\Entity
 */
class ActionRepository extends EntityRepository {

    /**
     * @param array $categoryIds
     * @param float $orderSummary
     * @return array
     */
    public function findActions($categoryIds, $orderSummary){

        $qb = $this->getEntityManager()->createQueryBuilder();

        $sql = '
            SELECT
                a.*
            FROM Action AS a
            JOIN action_category AS ac ON ac.action_id = a.id
        ';

        $expr = $qb->expr()->andX(
            $qb->expr()->eq('a.status', Action::STATUS_ON),
            $qb->expr()->in('ac.category_id', $categoryIds),
            $qb->expr()->orX(
                $qb->expr()->isNull('a.minOrderSummary'),
                $qb->expr()->lte('a.minOrderSummary', $orderSummary)
            ),
            $qb->expr()->orX(
                $qb->expr()->isNull('a.maxOrderSummary'),
                $qb->expr()->gte('a.maxOrderSummary', $orderSummary)
            )
        );

        $sql .= 'WHERE ' . (string)$expr;
        $sql .= '
            GROUP BY a.id
            ORDER BY a.position ASC
        ';

        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addEntityResult('ShopCatalogBundle:Action', 'a');

        foreach($this->getClassMetadata()->fieldNames as $columnName => $fieldName){
            $rsm->addFieldResult('a', $columnName, $fieldName);
        }

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);

        return $query->getResult();

    }

} 