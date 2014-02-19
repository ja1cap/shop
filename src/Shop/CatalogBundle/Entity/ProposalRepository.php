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
     * @param $categoryId
     * @param $proposalId
     * @param $filteredParameterValues
     * @return mixed
     */
    public function findProposalPrice($categoryId, $proposalId, $filteredParameterValues){

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select(array(
                'pp',
                'COUNT(DISTINCT ppv.id) AS HIDDEN values_amount',
            ))
            ->from('ShopCatalogBundle:Proposal', 'p')
            ->join('ShopCatalogBundle:Price', 'pp', Expr\Join::WITH, $qb->expr()->eq('pp.proposalId', 'p.id'))
            ->leftJoin('ShopCatalogBundle:ParameterValue', 'ppv', Expr\Join::WITH, $qb->expr()->eq('ppv.priceId', 'pp.id'));

        $qb->andWhere($qb->expr()->eq('p.id', $proposalId));

        $parametersExprList = $this->createParametersExprList($categoryId, $filteredParameterValues, $qb);
        $priceParametersExpr = $parametersExprList[1];

        if($priceParametersExpr){
            $qb->andWhere(call_user_func_array(array($qb->expr(), 'orX'), $priceParametersExpr));
            $qb->andHaving($qb->expr()->gte('values_amount', count($priceParametersExpr)));
        }

        $qb
            ->addOrderBy('pp.value', 'ASC')
            ->addGroupBy('pp.id');

        $query = $qb->getQuery();
        $query->setMaxResults(1);

        return current($query->getResult());

    }

    /**
     * @param $categoryId
     * @param $manufacturerId
     * @param $filteredParameterValues
     * @param null $page
     * @param null $perPage
     * @return array
     */
    public function findProposals($categoryId, $manufacturerId, $filteredParameterValues, $page = null, $perPage = null){

        $qb = $this->getEntityManager()->createQueryBuilder();

        list($parametersExpr, $priceParametersExpr) = $this->createParametersExprList($categoryId, $filteredParameterValues, $qb);

        $queryParameters = array(
            'category_id' => (int)$categoryId,
            'values_amount' => count($parametersExpr),
            'price_values_amount' => count($priceParametersExpr),
        );

        $sql = '
            SELECT
                p.*,
                pp.id AS priceId,
                pp.value AS price
            FROM Proposal AS p
        ';

        $sql .= '
            JOIN (
                SELECT
                    pp.id,
                    pp.value,
                    pp.proposalId
                FROM Price AS pp
                JOIN Proposal AS p ON p.id = pp.proposalId
                JOIN Category AS c ON c.id = p.categoryId
                LEFT JOIN ParameterValue AS ppv ON ppv.priceId = pp.id' . ($priceParametersExpr ? ' AND (' . call_user_func_array(array($qb->expr(), 'orX'), $priceParametersExpr). ')' : '') . '
                WHERE c.id  = :category_id
                GROUP BY pp.id
                HAVING COUNT(ppv.id) >= :price_values_amount
                ORDER BY pp.value
            ) AS pp ON pp.proposalId = p.id
        ';

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

        if($page && $perPage){
            $sql .= ' LIMIT ' . ($page > 1 ? (int)$page . ',' : '') . $perPage;
        }

        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addEntityResult('ShopCatalogBundle:Proposal', 'p', 'proposal');

        foreach($this->getClassMetadata()->fieldNames as $columnName => $fieldName){
            $rsm->addFieldResult('p', $columnName, $fieldName);
        }

        $rsm->addScalarResult('priceId', 'priceId', 'integer');
        $rsm->addScalarResult('price', 'price');

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $query->setParameters($queryParameters);

        return $query->getResult();

    }

    /**
     * @param $categoryId
     * @param $filteredParameterValues
     * @return array
     */
    public function findCategoryManufacturers($categoryId, $filteredParameterValues = array()){

        $qb = $this->getEntityManager()->createQueryBuilder();

        $queryParameters = array(
            'categoryId' => (int)$categoryId,
        );

        $sql = '
            SELECT
                m.*
            FROM Manufacturer AS m
            JOIN Proposal AS p ON p.categoryId = :categoryId AND p.manufacturerId = m.id
            JOIN Price AS pp ON pp.proposalId = p.id
        ';

        list($parametersExpr) = $this->createParametersExprList($categoryId, $filteredParameterValues, $qb);

        if($parametersExpr){

            $queryParameters['valuesAmount'] = count($parametersExpr);
            $sql .= ' LEFT JOIN ParameterValue AS pv ON pv.proposalId = p.id AND (' . call_user_func_array(array($qb->expr(), 'orX'), $parametersExpr). ')';

        }

        $sql .= '
            GROUP BY m.id
        ';

        if($parametersExpr){

            $sql .= '
                HAVING COUNT(pv.id) >= :valuesAmount
            ';

        }

        $sql .= '
            ORDER BY m.name
        ';

        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addEntityResult('ShopCatalogBundle:Manufacturer', 'm');

        foreach($this->getEntityManager()->getClassMetadata('ShopCatalogBundle:Manufacturer')->fieldNames as $columnName => $fieldName){
            $rsm->addFieldResult('m', $columnName, $fieldName);
        }

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $query->setParameters($queryParameters);

        return $query->getResult();

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

    /**
     * @param $categoryId
     * @param $filteredParameterValues
     * @param $qb
     * @return array
     */
    protected function createParametersExprList($categoryId, $filteredParameterValues, $qb)
    {
        $parametersExpr = array();
        $priceParametersExpr = array();

        if (is_array($filteredParameterValues)) {

            $filteredParameterValues = array_filter($filteredParameterValues);

            if ($filteredParameterValues) {

                $parameterIds = array_keys($filteredParameterValues);
                $categoryParameters = $this->getEntityManager()->getRepository('ShopCatalogBundle:CategoryParameter')->findBy(array(
                    'categoryId' => $categoryId,
                    'parameterId' => $parameterIds,
                ));

                /**
                 * @var $categoryParameter \Shop\CatalogBundle\Entity\CategoryParameter
                 */
                foreach ($categoryParameters as $categoryParameter) {

                    if (isset($filteredParameterValues[$categoryParameter->getParameterId()])) {

                        $parameter = $categoryParameter->getParameter();
                        $optionId = $filteredParameterValues[$parameter->getId()];

                        if ($parameter->getIsPriceParameter()) {

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
                return array($parametersExpr, $priceParametersExpr);

            }
            return array($parametersExpr, $priceParametersExpr);

        }
        return array($parametersExpr, $priceParametersExpr);
    }

} 