<?php
namespace Shop\CatalogBundle\Entity;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\ORM\QueryBuilder;
use Shop\MainBundle\Entity\AbstractRepository;

/**
 * Class ProposalRepository
 * @package Shop\CatalogBundle\Entity
 */
class ProposalRepository extends AbstractRepository {

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

        $parametersExprList = $this->createParametersExprList($categoryId, $filteredParameterValues, $qb);
        $priceParametersExpr = $parametersExprList[1];

        $pricesFilterSubQb = $this->getEntityManager()->createQueryBuilder();
        $pricesFilterSubQb
            ->select('pp.id')
            ->from('ShopCatalogBundle:Price', 'pp')
            ->join('ShopCatalogBundle:Proposal', 'p', Expr\Join::WITH, $qb->expr()->eq('p.id', 'pp.proposalId'))
            ->join('ShopCatalogBundle:Category', 'c', Expr\Join::WITH, $qb->expr()->eq('c.id', 'p.categoryId'))
            ->leftJoin('ShopCatalogBundle:ParameterValue', 'ppv', Expr\Join::WITH, $qb->expr()->andX(
                $qb->expr()->eq('ppv.priceId', 'pp.id'),
                ($priceParametersExpr ? call_user_func_array(array($qb->expr(), 'orX'), $priceParametersExpr) : null)
            ))
            ->where($qb->expr()->andX(
                $qb->expr()->eq('c.id', ':category_id'),
                $qb->expr()->eq('c.status', ':category_status'),
                $qb->expr()->eq('pp.status', ':price_status'),
                $qb->expr()->eq('p.id', ':proposal_id'),
                $qb->expr()->eq('p.status', ':proposal_status')
            ))
            ->groupBy('pp.id')
            ->having($qb->expr()->gte('COUNT(DISTINCT ppv.id)', ':price_values_amount'))
        ;
        $this->convertDqlToSql($pricesFilterSubQb);
        $pricesFilterSubQuerySql = (string)$pricesFilterSubQb;

        $qb
            ->select(array(
                'pp.*',
                '(CASE WHEN ccu.id IS NOT NULL THEN pp.value * ccu.value ELSE pp.value END) AS price',
                'MAX((CASE WHEN all_ccu.id IS NOT NULL THEN all_pp.value * all_ccu.value ELSE all_pp.value END)) AS maxPrice',
            ))
            ->from('ShopCatalogBundle:Proposal', 'p')
            ->join('ShopCatalogBundle:Price', 'pp', Expr\Join::WITH, $qb->expr()->in('pp.id', $pricesFilterSubQuerySql))
            ->leftJoin('ShopCatalogBundle:ContractorCurrency', 'ccu', Expr\Join::WITH, $qb->expr()->andX(
                $qb->expr()->eq('ccu.contractorId', 'pp.contractorId'),
                $qb->expr()->eq('ccu.numericCode', 'pp.currencyNumericCode')
            ))
            ->leftJoin('ShopCatalogBundle:Price', 'all_pp', Expr\Join::WITH, $qb->expr()->in('all_pp.id', $pricesFilterSubQuerySql))
            ->leftJoin('ShopCatalogBundle:ContractorCurrency', 'all_ccu', Expr\Join::WITH, $qb->expr()->andX(
                $qb->expr()->eq('all_ccu.contractorId', 'all_pp.contractorId'),
                $qb->expr()->eq('all_ccu.numericCode', 'all_pp.currencyNumericCode')
            ))
            ->andWhere($qb->expr()->eq('p.id', ':proposal_id'))
            ->addOrderBy('price', 'ASC')
            ->addGroupBy('pp.id')
        ;

        $this->convertDqlToSql($qb);
        $sql = (string)$qb . ' LIMIT 1';

        $rsm = $this->createResultSetMappingFromMetadata('ShopCatalogBundle:Price', 'pp', 'priceEntity');
        $rsm->addScalarResult('price', 'price');
        $rsm->addScalarResult('maxPrice', 'maxPrice');

        $queryParameters = array(
            'price_status' => Price::STATUS_ON,
            'proposal_id' => $proposalId,
            'proposal_status' => Proposal::STATUS_ON,
            'category_id' => (int)$categoryId,
            'category_status' => Category::STATUS_ON,
            'price_values_amount' => count($priceParametersExpr),
        );

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $query->setParameters($queryParameters);
        $result = $query->getResult();

//        foreach($queryParameters as $key => $value){
//            $sql = str_replace(':' . $key, $value, $sql);
//        }
//        echo($sql);
//        echo("<br/>");
//        die;

        return current($result);


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
            'manufacturer_id' => (int)$manufacturerId,
            'values_amount' => count($parametersExpr),
            'price_values_amount' => count($priceParametersExpr),
        );

        $pricesFilterSubQb = $this->getEntityManager()->createQueryBuilder();
        $pricesFilterSubQb
            ->select('pp.id')
            ->from('ShopCatalogBundle:Price', 'pp')
            ->join('ShopCatalogBundle:Proposal', 'p', Expr\Join::WITH, $qb->expr()->eq('p.id', 'pp.proposalId'))
            ->join('ShopCatalogBundle:Category', 'c', Expr\Join::WITH, $qb->expr()->eq('c.id', 'p.categoryId'))
            ->leftJoin('ShopCatalogBundle:ParameterValue', 'ppv', Expr\Join::WITH, $qb->expr()->andX(
                $qb->expr()->eq('ppv.priceId', 'pp.id'),
                ($priceParametersExpr ? call_user_func_array(array($qb->expr(), 'orX'), $priceParametersExpr) : null)
            ))
            ->where($qb->expr()->andX(
                $qb->expr()->eq('c.id', ':category_id'),
                $qb->expr()->eq('c.status', ':category_status'),
                $qb->expr()->eq('pp.status', ':price_status'),
                $qb->expr()->eq('p.status', ':proposal_status')
            ))
            ->groupBy('pp.id')
            ->having($qb->expr()->gte('COUNT(DISTINCT ppv.id)', ':price_values_amount'))
        ;
        $this->convertDqlToSql($pricesFilterSubQb);
        $pricesFilterSubQuerySql = (string)$pricesFilterSubQb;

        $qb
            ->select(array(
                'p.*',
                'MIN((CASE WHEN ccu.id IS NOT NULL THEN pp.value * ccu.value ELSE pp.value END)) AS price',
                'MAX((CASE WHEN ccu.id IS NOT NULL THEN pp.value * ccu.value ELSE pp.value END)) AS maxPrice',
            ))
            ->from('ShopCatalogBundle:Proposal', 'p')
            ->join('ShopCatalogBundle:Price', 'pp', Expr\Join::WITH, $qb->expr()->andX(
                $qb->expr()->eq('pp.proposalId', 'p.id'),
                $qb->expr()->in('pp.id', $pricesFilterSubQuerySql)
            ))
            ->leftJoin('ShopCatalogBundle:ContractorCurrency', 'ccu', Expr\Join::WITH, $qb->expr()->andX(
                $qb->expr()->eq('ccu.contractorId', 'pp.contractorId'),
                $qb->expr()->eq('ccu.numericCode', 'pp.currencyNumericCode')
            ))
            ->leftJoin('ShopCatalogBundle:ParameterValue', 'pv', Expr\Join::WITH, $qb->expr()->andX(
                $qb->expr()->eq('pv.proposalId', 'p.id'),
                ($parametersExpr ? call_user_func_array(array($qb->expr(), 'orX'), $parametersExpr) : null)
            ))
            ->andWhere($qb->expr()->andX(
                $qb->expr()->eq('p.categoryId', ':category_id'),
                ($manufacturerId ? $qb->expr()->eq('p.manufacturerId', ':manufacturer_id') : null)
            ))
            ->groupBy('p.id')
            ->andHaving($qb->expr()->gte('COUNT(DISTINCT pv.id)', ':values_amount'))
            ->addOrderBy('price')
        ;

        $this->convertDqlToSql($qb);
        $sql = (string)$qb;

        if($page && $perPage){
            $sql .= ' LIMIT ' . ($page > 1 ? (int)$page . ',' : '') . $perPage;
        }

        $rsm = $this->createResultSetMappingFromMetadata('ShopCatalogBundle:Proposal', 'p', 'proposal');
        $rsm->addScalarResult('price', 'price');
        $rsm->addScalarResult('maxPrice', 'maxPrice');

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $query->setParameters($queryParameters);

        $result = $query->getResult();

//        foreach($queryParameters as $key => $value){
//            $sql = str_replace(':' . $key, $value, $sql);
//        }
//        echo($sql);
//        echo("<br/>");
//        die;

        return $result;

    }

    /**
     * @param Parameter $parameter
     * @param $categoryId
     * @param $manufacturerId
     * @param $proposalId
     * @param $filteredParameterValues
     * @return array
     */
    public function getParameterOptionsAmounts(Parameter $parameter, $categoryId, $manufacturerId, $filteredParameterValues, $proposalId = null){

        $qb = $this->getEntityManager()->createQueryBuilder();
        list($parametersExpr, $priceParametersExpr) = $this->createParametersExprList($categoryId, $filteredParameterValues, $qb);

        $queryParameters = array(
            'priceStatus' => Price::STATUS_ON,
            'proposalStatus' => Proposal::STATUS_ON,
            'categoryStatus' => Category::STATUS_ON,
            'parameterId' => $parameter->getId(),
            'proposalId' => (int)$proposalId,
            'categoryId' => (int)$categoryId,
            'manufacturerId' => (int)$manufacturerId,
            'valuesAmount' => count($parametersExpr),
            'priceValuesAmount' => count($priceParametersExpr),
        );

        $sql = '
            SELECT
                po.id,
                COUNT(DISTINCT p.id) AS proposalsAmount,
                COUNT(DISTINCT pp.id) AS pricesAmount
            FROM
                ParameterOption AS po
            JOIN ParameterValue AS popv ON popv.optionId = po.id
        ';

        $filterSubQuerySql = null;

        if($parameter->getIsPriceParameter()){

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
                        _pp.id
                    FROM Proposal AS _p
                    JOIN Price AS _pp ON _pp.proposalId = _p.id
                    $parameterValueJoin
                    $priceParameterValueJoin
                    WHERE
                        _p.categoryId = :categoryId
                        " . ($proposalId ? 'AND _p.id = :proposalId' : '') . "
                        " . ($manufacturerId ? 'AND _p.manufacturerId = :manufacturerId' : '') . "
                        AND _pp.status = :proposalStatus
                        AND _p.status = :priceStatus
                    GROUP BY
                        _pp.id
                ";

                if($parametersExpr){

                    $filterSubQuerySql .= "
                        HAVING
                            COUNT(DISTINCT pv.id) >= :valuesAmount
                    ";

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

            $sql .= '
                JOIN Price AS pp ON pp.id = popv.priceId ' . ($filterSubQuerySql ? ' AND pp.id IN (' . $filterSubQuerySql . ')' : '') . '
                JOIN Proposal AS p ON p.id = pp.proposalId
            ';

        } else {

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
                    " . ($proposalId ? 'AND _p.id = :proposalId' : '') . "
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

            $sql .= '
                JOIN Proposal AS p ON p.id = popv.proposalId ' . ($filterSubQuerySql ? ' AND p.id IN (' . $filterSubQuerySql . ')' : '') . '
                JOIN Price AS pp ON pp.proposalId = p.id
            ';

        }

        $sql .= "
            WHERE
                po.parameterId = :parameterId
                AND p.categoryId = :categoryId
                " . ($proposalId ? 'AND p.id = :proposalId' : '') . "
                " . ($manufacturerId ? 'AND p.manufacturerId = :manufacturerId' : '') . "
                AND pp.status = :proposalStatus
                AND p.status = :priceStatus
            GROUP BY
                po.id;
        ";

//        foreach($queryParameters as $key => $value){
//            $sql = str_replace(':' . $key, $value, $sql);
//        }
//        echo($sql);
//        echo("<br/>");

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
     * @param $filteredParametersValues
     * @param QueryBuilder $qb
     * @return array
     */
    protected function createParametersExprList($categoryId, $filteredParametersValues, QueryBuilder $qb)
    {
        $parametersExpr = array();
        $priceParametersExpr = array();

        if (is_array($filteredParametersValues)) {

            $filteredParametersValues = array_filter($filteredParametersValues);

            if ($filteredParametersValues) {

                $parameterIds = array_keys($filteredParametersValues);
                $categoryParameters = $this->getEntityManager()->getRepository('ShopCatalogBundle:CategoryParameter')->findBy(array(
                    'categoryId' => $categoryId,
                    'parameterId' => $parameterIds,
                ));

                /**
                 * @var $categoryParameter \Shop\CatalogBundle\Entity\CategoryParameter
                 */
                foreach ($categoryParameters as $categoryParameter) {

                    if (isset($filteredParametersValues[$categoryParameter->getParameterId()])) {

                        $parameter = $categoryParameter->getParameter();
                        $filteredParameterValue = $filteredParametersValues[$parameter->getId()];

                        if(is_array($filteredParameterValue)){

                            if ($parameter->getIsPriceParameter()) {

                                $priceParametersExpr[] = $qb->expr()->andX(
                                    $qb->expr()->eq('ppv.parameterId', $parameter->getId()),
                                    $qb->expr()->in('ppv.optionId', $filteredParameterValue)
                                );

                            } else {

                                $parametersExpr[] = $qb->expr()->andX(
                                    $qb->expr()->eq('pv.parameterId', $parameter->getId()),
                                    $qb->expr()->in('pv.optionId', $filteredParameterValue)
                                );

                            }

                        } else {

                            $optionId = (int)$filteredParameterValue;

                            if ($parameter->getIsPriceParameter()) {

                                $priceParametersExpr[] = $qb->expr()->andX(
                                    $qb->expr()->eq('ppv.parameterId', $parameter->getId()),
                                    $qb->expr()->eq('ppv.optionId', $optionId)
                                );

                            } else {

                                $parametersExpr[] = $qb->expr()->andX(
                                    $qb->expr()->eq('pv.parameterId', $parameter->getId()),
                                    $qb->expr()->eq('pv.optionId', $optionId)
                                );

                            }

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