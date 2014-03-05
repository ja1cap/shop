<?php
namespace Shop\CatalogBundle\Entity;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\ORM\QueryBuilder;

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
            ->join('ShopCatalogBundle:Proposal', 'p', Expr\Join::WITH, $qb->expr()->andX(
                $qb->expr()->eq('p.status', Proposal::STATUS_ON),
                $qb->expr()->eq('p.id', $proposalId)
            ))
            ->join('ShopCatalogBundle:Category', 'c', Expr\Join::WITH, $qb->expr()->andX(
                $qb->expr()->eq('c.status', Category::STATUS_ON),
                $qb->expr()->eq('c.id', 'p.categoryId')
            ))
            ->join('ShopCatalogBundle:CategoryParameter', 'cp', Expr\Join::WITH, $qb->expr()->andX(
                $qb->expr()->eq('cp.categoryId', 'p.categoryId'),
                $qb->expr()->eq('cp.parameterId', 'po.parameterId')
            ))
            ->join('ShopCatalogBundle:Price', 'pp', Expr\Join::WITH, $qb->expr()->andX(
                $qb->expr()->eq('pp.status', Price::STATUS_ON),
                $qb->expr()->eq('pp.proposalId', 'p.id')
            ))
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
                '(CASE WHEN ccu.id IS NOT NULL THEN pp.value * ccu.value ELSE pp.value END) AS HIDDEN price',
                'COUNT(DISTINCT ppv.id) AS HIDDEN values_amount',
            ))
            ->from('ShopCatalogBundle:Proposal', 'p')
            ->join('ShopCatalogBundle:Price', 'pp', Expr\Join::WITH, $qb->expr()->andX(
                $qb->expr()->eq('pp.status', Price::STATUS_ON),
                $qb->expr()->eq('pp.proposalId', 'p.id')
            ))
            ->leftJoin('ShopCatalogBundle:ContractorCurrency', 'ccu', Expr\Join::WITH, $qb->expr()->andX(
                $qb->expr()->eq('ccu.contractorId', 'pp.contractorId'),
                $qb->expr()->eq('ccu.numericCode', 'pp.currencyNumericCode')
            ))
            ->leftJoin('ShopCatalogBundle:ParameterValue', 'ppv', Expr\Join::WITH, $qb->expr()->eq('ppv.priceId', 'pp.id'));

        $qb->andWhere($qb->expr()->eq('p.id', $proposalId));

        $parametersExprList = $this->createParametersExprList($categoryId, $filteredParameterValues, $qb);
        $priceParametersExpr = $parametersExprList[1];

        if($priceParametersExpr){
            $qb->andWhere(call_user_func_array(array($qb->expr(), 'orX'), $priceParametersExpr));
            $qb->andHaving($qb->expr()->gte('values_amount', count($priceParametersExpr)));
        }

        $qb
            ->addOrderBy('price', 'ASC')
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
            'price_status' => Price::STATUS_ON,
            'proposal_status' => Proposal::STATUS_ON,
            'category_status' => Category::STATUS_ON,
            'category_id' => (int)$categoryId,
            'values_amount' => count($parametersExpr),
            'price_values_amount' => count($priceParametersExpr),
        );

        $sql = '
            SELECT
                p.*,
                pp.id AS priceId,
                pp.price AS price
            FROM Proposal AS p
        ';

        $sql .= '
            JOIN (
                SELECT
                    pp.id,
                    (CASE WHEN ccu.id IS NOT NULL THEN pp.value * ccu.value ELSE pp.value END) AS price,
                    pp.proposalId
                FROM Price AS pp
                JOIN Proposal AS p ON p.id = pp.proposalId
                JOIN Category AS c ON c.id = p.categoryId
                LEFT JOIN ContractorCurrency AS ccu ON ccu.contractorId = pp.contractorId AND ccu.numericCode = pp.currencyNumericCode
                LEFT JOIN ParameterValue AS ppv ON ppv.priceId = pp.id' . ($priceParametersExpr ? ' AND (' . call_user_func_array(array($qb->expr(), 'orX'), $priceParametersExpr). ')' : '') . '
                WHERE c.id = :category_id
                    AND pp.status = :price_status
                    AND p.status = :proposal_status
                    AND c.status = :category_status
                GROUP BY pp.id
                HAVING COUNT(DISTINCT ppv.id) >= :price_values_amount
                ORDER BY price DESC
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
            HAVING COUNT(DISTINCT pv.id) >= :values_amount
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
//        echo($query->getSQL());
//        var_dump($queryParameters);

        $result = $query->getResult();
        //var_dump($result);

        return $result;

    }

    /**
     * @param Parameter $parameter
     * @param $categoryId
     * @param $manufacturerId
     * @param $filteredParameterValues
     * @return array
     */
    public function getParameterOptionsAmounts(Parameter $parameter, $categoryId, $manufacturerId, $filteredParameterValues){

        $qb = $this->getEntityManager()->createQueryBuilder();
        list($parametersExpr, $priceParametersExpr) = $this->createParametersExprList($categoryId, $filteredParameterValues, $qb);

        $queryParameters = array(
            'priceStatus' => Price::STATUS_ON,
            'proposalStatus' => Proposal::STATUS_ON,
            'categoryStatus' => Category::STATUS_ON,
            'parameterId' => $parameter->getId(),
            'categoryId' => (int)$categoryId,
            'manufacturerId' => (int)$manufacturerId,
            'valuesAmount' => count($parametersExpr),
            'priceValuesAmount' => count($priceParametersExpr),
        );

        $filterSubQuerySql = '';

        if($parametersExpr || $priceParametersExpr){

            $parameterValueJoin = '';
            if($parametersExpr){
                $parameterValueJoin = '
                    LEFT JOIN ParameterValue AS pv ON pv.proposalId = _p.id AND (' . call_user_func_array(array($qb->expr(), 'orX'), $parametersExpr). ')
                ';
            }

            $priceParameterValueJoin = '';
            if($priceParametersExpr){

                $priceParameterValueJoin = '
                    LEFT JOIN ParameterValue AS ppv ON ppv.priceId = _pp.id AND (' . call_user_func_array(array($qb->expr(), 'orX'), $priceParametersExpr). ')
                ';

            }

            $filterSubQuerySql = "
                SELECT
                    _p.id
                FROM Proposal AS _p
                JOIN Price AS _pp ON _pp.proposalId = _p.id
                $parameterValueJoin
                $priceParameterValueJoin
                WHERE
                    _p.categoryId = :categoryId
                    " . ($manufacturerId ? 'AND _p.manufacturerId = :manufacturerId' : '') . "
                    AND _pp.status = :proposalStatus
                    AND _p.status = :priceStatus
                GROUP BY
                    _p.id
            ";

            if($parametersExpr){

                $filterSubQuerySql .= '
                    HAVING
                        COUNT(DISTINCT pv.id) >= :valuesAmount
                ';

            }

            if($priceParametersExpr){

                if($parametersExpr){

                    $filterSubQuerySql .= ' AND ';

                } else {

                    $filterSubQuerySql .= ' HAVING  ';

                }

                $filterSubQuerySql .= 'COUNT(DISTINCT ppv.id) >= :priceValuesAmount';

            }

        }


        $sql = '
            SELECT
                po.id,
                COUNT(DISTINCT p.id) AS proposalsAmount
            FROM
                ParameterOption AS po
            JOIN ParameterValue AS popv ON popv.optionId = po.id
        ';

        if($parameter->getIsPriceParameter()){

            $sql .= '
                JOIN Price AS pp ON pp.id = popv.priceId ' . ($filterSubQuerySql ? ' AND pp.proposalId IN (' . $filterSubQuerySql . ')' : '') . '
                JOIN Proposal AS p ON p.id = pp.proposalId
            ';

        } else {

            $sql .= '
                JOIN Proposal AS p ON p.id = popv.proposalId ' . ($filterSubQuerySql ? ' AND p.id IN (' . $filterSubQuerySql . ')' : '') . '
                JOIN Price AS pp ON pp.proposalId = p.id
            ';

        }

        $sql .= "
            WHERE
                po.parameterId = :parameterId
                AND p.categoryId = :categoryId
                " . ($manufacturerId ? 'AND p.manufacturerId = :manufacturerId' : '') . "
                AND pp.status = :proposalStatus
                AND p.status = :priceStatus

            GROUP BY
                po.id;
        ";

        $result = $this->getEntityManager()->getConnection()->fetchAll($sql, $queryParameters);
        $optionsAmounts = array();

        foreach($result as $optionAmounts){
            $optionsAmounts[$optionAmounts['id']] = $optionAmounts;
        }

        return $optionsAmounts;

    }

    /**
     * @param $categoryId
     * @param $filteredParameterValues
     * @return array
     */
    public function findCategoryManufacturers($categoryId, $filteredParameterValues = array()){

        $qb = $this->getEntityManager()->createQueryBuilder();

        $queryParameters = array(
            'priceStatus' => Price::STATUS_ON,
            'categoryId' => (int)$categoryId,
        );

        $sql = '
            SELECT
                m.*
            FROM Manufacturer AS m
            JOIN Proposal AS p ON p.categoryId = :categoryId AND p.manufacturerId = m.id
            JOIN Price AS pp ON pp.proposalId = p.id AND pp.status = :priceStatus
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
                HAVING COUNT(DISTINCT pv.id) >= ' . count($parametersExpr) . '
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
                $qb->expr()->eq('p.status', Proposal::STATUS_ON),
                $qb->expr()->eq('p.categoryId', $categoryId),
                $qb->expr()->eq('p.id', 'pv.proposalId')
            ))
            ->leftJoin('ShopCatalogBundle:Proposal', '_p', Expr\Join::WITH, $qb->expr()->andX(
                $qb->expr()->eq('_p.status', Proposal::STATUS_ON),
                $qb->expr()->eq('_p.categoryId', $categoryId)
            ))
            ->leftJoin('ShopCatalogBundle:Price', 'pp', Expr\Join::WITH, $qb->expr()->andX(
                $qb->expr()->eq('pp.status', Price::STATUS_ON),
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
     * @param QueryBuilder $qb
     * @return array
     */
    protected function createParametersExprList($categoryId, $filteredParameterValues, QueryBuilder $qb)
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