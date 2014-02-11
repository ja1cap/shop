<?php
namespace Shop\CatalogBundle\Entity;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

/**
 * Class ProposalRepository
 * @package Shop\CatalogBundle\Entity
 */
class ProposalRepository extends EntityRepository {

    /**
     * @param $proposalId
     * @return array
     */
    public function findProposalParametersOptions($proposalId){

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select('po')
            ->from('ShopCatalogBundle:ParameterOption', 'po')
            ->join('ShopCatalogBundle:Proposal', 'p', Expr\Join::WITH, $qb->expr()->eq('p.id', $proposalId))
            ->join('ShopCatalogBundle:CategoryParameter', 'cp', Expr\Join::WITH, $qb->expr()->andX(
                $qb->expr()->eq('cp.categoryId', 'p.categoryId'),
                $qb->expr()->eq('cp.parameterId', 'po.parameterId')
            ))
            ->join('ShopCatalogBundle:Price', 'pp', Expr\Join::WITH, $qb->expr()->eq('pp.proposalId', 'p.id'))
            ->join('ShopCatalogBundle:ParameterValue', 'pv', Expr\Join::WITH, $qb->expr()->andX(
                $qb->expr()->eq('pv.optionId', 'po.id'),
                $qb->expr()->eq('pv.priceId', 'pp.id')
            ));

        $qb
            ->addOrderBy('cp.position')
            ->addOrderBy('po.position')
            ->groupBy('po.id');

        return $qb->getQuery()->getResult();

    }

    /**
     * @param $proposalId
     * @param $filteredParameterValues
     * @return mixed
     */
    public function findProposalPrice($proposalId, $filteredParameterValues){

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select(array(
                'pp',
                'COUNT(DISTINCT pv.id) AS HIDDEN values_amount',
            ))
            ->from('ShopCatalogBundle:Proposal', 'p')
            ->join('ShopCatalogBundle:Price', 'pp', Expr\Join::WITH, $qb->expr()->eq('pp.proposalId', 'p.id'))
            ->leftJoin('ShopCatalogBundle:ParameterValue', 'pv', Expr\Join::WITH, $qb->expr()->eq('pv.priceId', 'pp.id'));

        $qb->andWhere($qb->expr()->eq('p.id', $proposalId));

        if(is_array($filteredParameterValues)){

            $parametersExpr = array();
            $filteredParameterValues = array_filter($filteredParameterValues);

            foreach($filteredParameterValues as $parameterId => $optionId){

                $parametersExpr[] = $qb->expr()->andX(
                    $qb->expr()->eq('pv.parameterId', $parameterId),
                    $qb->expr()->eq('pv.optionId', $optionId)
                );

            }

            if($parametersExpr){

                $qb->andWhere(call_user_func_array(array($qb->expr(), 'orX'), $parametersExpr));
                $qb->andHaving($qb->expr()->gte('values_amount', count($filteredParameterValues)));

            }

        }

        $qb
            ->addOrderBy('pp.value', 'ASC')
            ->addGroupBy('pp.id');

        $query = $qb->getQuery();
        $query->setMaxResults(1);

        return current($query->getResult());

    }

    public function findProposals($categoryId, $manufacturerId, $filteredParameterValues){

        $qb = $this->getEntityManager()->createQueryBuilder();

        $parametersExpr = array();
        $priceParametersExpr = array();

        if(is_array($filteredParameterValues)){

            $filteredParameterValues = array_filter($filteredParameterValues);

            if($filteredParameterValues){

                $parameterIds = array_keys($filteredParameterValues);
                $categoryParameters = $this->getEntityManager()->getRepository('ShopCatalogBundle:CategoryParameter')->findBy(array(
                    'categoryId' => $categoryId,
                    'parameterId' => $parameterIds,
                ));

                /**
                 * @var $categoryParameter \Shop\CatalogBundle\Entity\CategoryParameter
                 */
                foreach($categoryParameters as $categoryParameter){

                    if(isset($filteredParameterValues[$categoryParameter->getParameterId()])){

                        $parameter = $categoryParameter->getParameter();
                        $optionId = $filteredParameterValues[$parameter->getId()];

                        if($parameter->getIsPriceParameter()){

                            $priceParametersExpr[] = $qb->expr()->andX(
                                $qb->expr()->eq('ppv.parameterId', $parameter->getId()),
                                $qb->expr()->eq('ppv.optionId', (int)$optionId)
                            );

                        } else {

                            $parametersExpr[] = $qb->expr()->andX(
                                $qb->expr()->eq('pv.parameterId', $parameter->getId()),
                                $qb->expr()->eq('pv.optionId', (int)$optionId)
                            );

                        }

                    }

                }

            }

        }

        $queryParameters = array(
            'category_id' => (int)$categoryId,
            'values_amount' => count($parametersExpr),
            'price_values_amount' => count($priceParametersExpr),
        );

        $sql = '
            SELECT
                p.*,
                MIN(pp.value) AS price
            FROM Proposal AS p
        ';

        $sql .= ' JOIN Price AS pp ON pp.proposal_id = p.id';
        if($priceParametersExpr){

            $sql .= ' AND pp.id IN (
                SELECT
                    pp.id
                FROM Price AS pp
                JOIN Proposal AS p ON p.id = pp.proposal_id
                JOIN Category AS c ON c.id = p.categoryId
                JOIN ParameterValue AS ppv ON ppv.priceId = pp.id AND (' . call_user_func_array(array($qb->expr(), 'orX'), $priceParametersExpr). ')
                WHERE c.id  = :category_id
                GROUP BY pp.id
                HAVING COUNT(ppv.id) >= :price_values_amount
                ORDER BY pp.value
            )';

        }

        $sql .= ' LEFT JOIN ParameterValue AS pv ON pv.proposalId = p.id';
        if($parametersExpr){
            $sql .= ' AND (' . call_user_func_array(array($qb->expr(), 'orX'), $parametersExpr). ')';
        }

        $sql .= ' WHERE p.categoryId = :category_id';
        if($manufacturerId){
            $queryParameters['manufacturer_id'] = (int)$manufacturerId;
            $sql .= ' AND p.manufacturerId = :manufacturer_id';
        }

        $sql .= '
            GROUP BY p.id
            HAVING COUNT(pv.id) >= :values_amount
            ORDER BY price
        ';

        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addEntityResult('ShopCatalogBundle:Proposal', 'p', 'proposal');

        foreach($this->getClassMetadata()->fieldNames as $columnName => $fieldName){
            $rsm->addFieldResult('p', $columnName, $fieldName);
        }

        $rsm->addScalarResult('price', 'price');

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $query->setParameters($queryParameters);

        return $query->getResult();

    }

    /**
     * @param $categoryId
     * @return array
     */
    public function findCategoryManufacturers($categoryId){

        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb
            ->select('m')
            ->from('ShopCatalogBundle:Manufacturer', 'm')
            ->join('ShopCatalogBundle:Proposal', 'p', Expr\Join::WITH, $qb->expr()->andX(
                $qb->expr()->eq('p.categoryId', $categoryId),
                $qb->expr()->eq('p.manufacturerId', 'm.id')
            ));

        $qb->addOrderBy('m.name');
        $qb->addGroupBy('m.id');

        return $qb->getQuery()->getResult();

    }

    /**
     * @param $categoryId
     * @return array
     */
    public function findCategoryParametersOptions($categoryId){

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select('po')
            ->from('ShopCatalogBundle:ParameterOption', 'po')
            ->join('ShopCatalogBundle:CategoryParameter', 'cp', Expr\Join::WITH, $qb->expr()->andX(
                $qb->expr()->eq('cp.categoryId', $categoryId),
                $qb->expr()->eq('cp.parameterId', 'po.parameterId')
            ))
            ->join('ShopCatalogBundle:ParameterValue', 'pv', Expr\Join::WITH, $qb->expr()->eq('pv.optionId', 'po.id'))
            ->leftJoin('ShopCatalogBundle:Proposal', 'p', Expr\Join::WITH, $qb->expr()->andX(
                $qb->expr()->eq('p.categoryId', $categoryId),
                $qb->expr()->eq('p.id', 'pv.proposalId')
            ))
            ->leftJoin('ShopCatalogBundle:Proposal', '_p', Expr\Join::WITH, $qb->expr()->eq('_p.categoryId', $categoryId))
            ->leftJoin('ShopCatalogBundle:Price', 'pp', Expr\Join::WITH, $qb->expr()->andX(
                $qb->expr()->eq('pp.proposalId', '_p.id'),
                $qb->expr()->eq('pp.id', 'pv.priceId')
            ));

        $qb->andWhere($qb->expr()->orX(
            $qb->expr()->isNotNull('p.id'),
            $qb->expr()->isNotNull('pp.id')
        ));

        $qb
            ->addOrderBy('cp.position')
            ->addOrderBy('po.position')
            ->groupBy('po.id');

//        echo($qb);die;

        return $qb->getQuery()->getResult();

    }

} 