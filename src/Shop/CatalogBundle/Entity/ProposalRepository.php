<?php
namespace Shop\CatalogBundle\Entity;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\ORM\QueryBuilder;
use Weasty\DoctrineBundle\Entity\AbstractRepository;

/**
 * Class ProposalRepository
 * @package Shop\CatalogBundle\Entity
 */
class ProposalRepository extends AbstractRepository {

    /**
     * @param $formattedNames
     * @return array
     */
    public function findProposalsByName($formattedNames){

        if(!$formattedNames){
            return array();
        }

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select('p.*')
            ->from('ShopCatalogBundle:Proposal', 'p')
            ->andWhere($qb->expr()->in("REPLACE(LOWER(p.title), ' ', '')", $formattedNames))
        ;

        $rsm = $this->createResultSetMappingFromMetadata('ShopCatalogBundle:Proposal', 'p');

        $sql = (string)$this->convertDqlToSql($qb);
        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);

        return $query->getResult();

    }

    /**
     * @param $categoryId
     * @return array
     */
    public function getProposalPriceRange($categoryId){

        $queryParameters = array(
            'price_status' => Price::STATUS_ON,
            'proposal_status' => Proposal::STATUS_ON,
            'category_status' => Category::STATUS_ON,
            'category_id' => (int)$categoryId,
        );

        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb
            ->select(array(
                'MIN((CASE WHEN ccu.id IS NOT NULL THEN pp.value * ccu.value ELSE pp.value END)) AS minPrice',
                'MAX((CASE WHEN ccu.id IS NOT NULL THEN pp.value * ccu.value ELSE pp.value END)) AS maxPrice',
            ))
            ->from('ShopCatalogBundle:Proposal', 'p')
            ->join('ShopCatalogBundle:Category', 'c', Expr\Join::WITH, $qb->expr()->eq('c.id', 'p.categoryId'))
            ->join('ShopCatalogBundle:Price', 'pp', Expr\Join::WITH, $qb->expr()->eq('pp.proposalId', 'p.id'))
            ->leftJoin('ShopCatalogBundle:ContractorCurrency', 'ccu', Expr\Join::WITH, $qb->expr()->andX(
                $qb->expr()->eq('ccu.contractorId', 'pp.contractorId'),
                $qb->expr()->eq('ccu.numericCode', 'pp.currencyNumericCode')
            ))
            ->andWhere($qb->expr()->andX(
                $qb->expr()->eq('c.id', ':category_id'),
                $qb->expr()->eq('c.status', ':category_status'),
                $qb->expr()->eq('pp.status', ':price_status'),
                $qb->expr()->eq('p.status', ':proposal_status')
            ))
        ;

        $sql = (string)$this->convertDqlToSql($qb);
        return $this->getEntityManager()->getConnection()->fetchAssoc($sql, $queryParameters);

    }

    /**
     * @param $categoryId
     * @param $manufacturerId
     * @param $filterParametersValues
     * @param null $proposalId
     * @return array
     */
    public function getPriceIntervalsData($categoryId, $manufacturerId, $filterParametersValues, $proposalId = null){

        $priceStep = $this->getProposalPriceRange($categoryId);
        $minPrice = $priceStep['minPrice'];
        $maxPrice = $priceStep['maxPrice'];

//        $minPriceLength = strlen($minPrice);
//        $maxPriceLength = strlen($maxPrice);
        $averagePriceLength = strlen(($minPrice + $maxPrice)/2);

//        $minPriceExponent = ($minPriceLength - 1);
//        $maxPriceExponent = ($maxPriceLength - 1);
//        $averagePriceExponent = ($averagePriceLength - 1);
//
//        var_dump($minPriceExponent);
//        var_dump($maxPriceExponent);
//        var_dump($averagePriceExponent);
//        die;

        $priceExponent = 1;
        if($averagePriceLength > 1){

            $priceExponent = ($averagePriceLength - 1);

            if($priceExponent > 6){

                $priceExponent = 6;

            }

        }

        $queryParameters = array(
            'price_status' => Price::STATUS_ON,
            'proposal_status' => Proposal::STATUS_ON,
            'category_status' => Category::STATUS_ON,
            'proposal_id' => (int)$proposalId,
            'category_id' => (int)$categoryId,
            'manufacturer_id' => (int)$manufacturerId,
            'price_exponent' => $priceExponent,
        );

        $priceInterval = pow(10, $priceExponent);
        $priceIntervalsAmount = round($maxPrice / $priceInterval);

        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb
            ->select(array(
                'FLOOR((CASE WHEN ccu.id IS NOT NULL THEN pp.value * ccu.value ELSE pp.value END) / POW(10, :price_exponent)) * POW(10, :price_exponent) AS priceStep',
            ))
            ->from('ShopCatalogBundle:Proposal', 'p')
            ->join('ShopCatalogBundle:Price', 'pp', Expr\Join::WITH, $qb->expr()->eq('pp.proposalId', 'p.id'))
            ->leftJoin('ShopCatalogBundle:ContractorCurrency', 'ccu', Expr\Join::WITH, $qb->expr()->andX(
                $qb->expr()->eq('ccu.contractorId', 'pp.contractorId'),
                $qb->expr()->eq('ccu.numericCode', 'pp.currencyNumericCode')
            ))
            ->andWhere($qb->expr()->andX(
                $qb->expr()->eq('p.categoryId', ':category_id'),
                $qb->expr()->eq('pp.status', ':price_status'),
                $qb->expr()->eq('p.status', ':proposal_status')
            ))
            ->groupBy('priceStep')
            ->having($qb->expr()->gte('COUNT(DISTINCT p.id)', 0))
            ->orderBy('priceStep', 'ASC')
        ;

        $this->convertDqlToSql($qb);
        $sql = (string)$qb;
        $existingPriceSteps = $this->getEntityManager()->getConnection()->executeQuery($sql, $queryParameters)->fetchAll(\PDO::FETCH_COLUMN);

        $priceIntervals = array();

        $minPriceStep = floor($minPrice / $priceInterval) * $priceInterval;
        for($i = 0; $i <= $priceIntervalsAmount; $i++){

            $priceStep = $i * $priceInterval;

            if($priceStep >= $minPriceStep && in_array($priceStep, $existingPriceSteps)){

                $priceIntervals[$priceStep] = array(
                    'priceStep' => $priceStep,
                    'proposalsAmount' => 0,
                    'pricesAmount' => 0,
                );

            }

        }

        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb
            ->select(array(
                'FLOOR((CASE WHEN ccu.id IS NOT NULL THEN pp.value * ccu.value ELSE pp.value END) / POW(10, :price_exponent)) * POW(10, :price_exponent) AS priceStep',
                'COUNT(DISTINCT p.id) as proposalsAmount',
                'COUNT(DISTINCT pp.id) as pricesAmount',
            ))
            ->from('ShopCatalogBundle:Proposal', 'p')
        ;

        $pricesFilterSubQb = $this->getEntityManager()->createQueryBuilder();
        $pricesFilterSubQb
            ->select('DISTINCT pp.id')
            ->from('ShopCatalogBundle:Price', 'pp')
            ->join('ShopCatalogBundle:Proposal', 'p', Expr\Join::WITH, $qb->expr()->eq('p.id', 'pp.proposalId'))
            ->join('ShopCatalogBundle:Category', 'c', Expr\Join::WITH, $qb->expr()->eq('c.id', 'p.categoryId'))
            ->andWhere($qb->expr()->andX(
                $qb->expr()->eq('c.id', ':category_id'),
                $qb->expr()->eq('c.status', ':category_status'),
                $qb->expr()->eq('pp.status', ':price_status'),
                $qb->expr()->eq('p.status', ':proposal_status'),
                ($proposalId ? $qb->expr()->eq('p.id', ':proposal_id') : null)
            ))
        ;

        $this->applyParametersFilters($categoryId, $filterParametersValues, $qb, $pricesFilterSubQb);

        $this->convertDqlToSql($pricesFilterSubQb);
        $pricesFilterSubQuerySql = (string)$pricesFilterSubQb;

        $qb
            ->join('ShopCatalogBundle:Price', 'pp', Expr\Join::WITH, $qb->expr()->andX(
                $qb->expr()->eq('pp.proposalId', 'p.id'),
                $qb->expr()->in('pp.id', $pricesFilterSubQuerySql)
            ))
            ->leftJoin('ShopCatalogBundle:ContractorCurrency', 'ccu', Expr\Join::WITH, $qb->expr()->andX(
                $qb->expr()->eq('ccu.contractorId', 'pp.contractorId'),
                $qb->expr()->eq('ccu.numericCode', 'pp.currencyNumericCode')
            ))
        ;

        $qb
            ->andWhere($qb->expr()->andX(
                $qb->expr()->eq('p.categoryId', ':category_id'),
                ($proposalId ? $qb->expr()->eq('p.id', ':proposal_id') : null),
                ($manufacturerId ? $qb->expr()->eq('p.manufacturerId', ':manufacturer_id') : null)
            ))
            ->groupBy('priceStep')
            ->orderBy('priceStep', 'ASC')
        ;

        $this->convertDqlToSql($qb);
        $sql = (string)$qb;

//        foreach($queryParameters as $key => $value){
//            $sql = str_replace(':' . $key, $value, $sql);
//        }
//        echo($sql);
//        echo("<br/>");
//        die;

        $priceStepsAmounts = $this->getEntityManager()->getConnection()->fetchAll($sql, $queryParameters);
        foreach($priceStepsAmounts as $priceStepAmounts){

            $priceStep = $priceStepAmounts['priceStep'];
            if(isset($priceIntervals[$priceStep])){
                $priceIntervals[$priceStep] = $priceStepAmounts;
            }

        }

        return array(
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
            'interval' => $priceInterval,
            'intervals' => $priceIntervals,
        );

    }

    /**
     * @param $categoryId
     * @param $proposalId
     * @param $filteredParametersValues
     * @param $filterPricesRanges
     * @return mixed
     */
    public function findProposalPrice($categoryId, $proposalId, $filteredParametersValues, $filterPricesRanges){

        $qb = $this->getEntityManager()->createQueryBuilder();

        $pricesFilterSubQb = $this->getEntityManager()->createQueryBuilder();
        $pricesFilterSubQb
            ->select('DISTINCT pp.id')
            ->from('ShopCatalogBundle:Price', 'pp')
            ->join('ShopCatalogBundle:Proposal', 'p', Expr\Join::WITH, $qb->expr()->eq('p.id', 'pp.proposalId'))
            ->join('ShopCatalogBundle:Category', 'c', Expr\Join::WITH, $qb->expr()->eq('c.id', 'p.categoryId'))
            ->andWhere($qb->expr()->andX(
                $qb->expr()->eq('c.id', ':category_id'),
                $qb->expr()->eq('c.status', ':category_status'),
                $qb->expr()->eq('pp.status', ':price_status'),
                $qb->expr()->eq('p.id', ':proposal_id'),
                $qb->expr()->eq('p.status', ':proposal_status')
            ))
        ;

        $this->applyParametersFilters($categoryId, $filteredParametersValues, null, $pricesFilterSubQb);
        $this->applyPriceFilter($filterPricesRanges, $pricesFilterSubQb);
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
        );

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $query->setParameters($queryParameters);

//        foreach($queryParameters as $key => $value){
//            $sql = str_replace(':' . $key, $value, $sql);
//        }
//        echo($sql);
//        echo("<br/>");
//        die;

        $result = $query->getResult();

        return current($result);


    }

    public function findProposals($categoryId, $contractorsIds = null, $manufacturersIds = null){

        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb
            ->select(array(
                'p',
                'coalesce(p.defaultContractorId, pp.contractorId) AS HIDDEN _contractorId'
            ))
            ->from('ShopCatalogBundle:Proposal', 'p')
            ->leftJoin('ShopCatalogBundle:Price', 'pp', Expr\Join::LEFT_JOIN, $qb->expr()->eq('pp.proposalId', 'p.id'))
        ;

        if($categoryId){
            $qb->andWhere($qb->expr()->eq('p.categoryId', $categoryId));
        }

        if($contractorsIds){
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->andX(
                        $qb->expr()->in('p.defaultContractorId', $contractorsIds),
                        $qb->expr()->isNull('pp.contractorId')
                    ),
                    $qb->expr()->in('pp.contractorId', $contractorsIds)
                )
            );
        }

        if($manufacturersIds){
            $qb->andWhere($qb->expr()->in('p.manufacturerId', $manufacturersIds));
        }

        $qb
            ->addOrderBy('p.manufacturerId')
            ->addOrderBy('_contractorId')
        ;

        $query = $qb->getQuery();

        return $query->getResult();

    }

    /**
     * @param $categoryId
     * @param $manufacturerId
     * @param $filteredParametersValues
     * @param $filterPricesRanges
     * @param null $page
     * @param null $perPage
     * @return array
     */
    public function findProposalsByParameters($categoryId, $manufacturerId, $filteredParametersValues, $filterPricesRanges, $page = null, $perPage = null){

        $qb = $this->getEntityManager()->createQueryBuilder();

        $queryParameters = array(
            'price_status' => Price::STATUS_ON,
            'proposal_status' => Proposal::STATUS_ON,
            'category_status' => Category::STATUS_ON,
            'category_id' => (int)$categoryId,
            'manufacturer_id' => (int)$manufacturerId,
        );

        $qb
            ->select(array(
                'p.*',
                'MIN((CASE WHEN ccu.id IS NOT NULL THEN pp.value * ccu.value ELSE pp.value END)) AS price',
                'MAX((CASE WHEN ccu.id IS NOT NULL THEN pp.value * ccu.value ELSE pp.value END)) AS maxPrice',
            ))
            ->from('ShopCatalogBundle:Proposal', 'p')
        ;

        $pricesFilterSubQb = $this->getEntityManager()->createQueryBuilder();
        $pricesFilterSubQb
            ->select('DISTINCT pp.id')
            ->from('ShopCatalogBundle:Price', 'pp')
            ->join('ShopCatalogBundle:Proposal', 'p', Expr\Join::WITH, $qb->expr()->eq('p.id', 'pp.proposalId'))
            ->join('ShopCatalogBundle:Category', 'c', Expr\Join::WITH, $qb->expr()->eq('c.id', 'p.categoryId'))
            ->andWhere($qb->expr()->andX(
                $qb->expr()->eq('c.id', ':category_id'),
                $qb->expr()->eq('c.status', ':category_status'),
                $qb->expr()->eq('pp.status', ':price_status'),
                $qb->expr()->eq('p.status', ':proposal_status')
            ))
        ;

        $this->applyParametersFilters($categoryId, $filteredParametersValues, $qb, $pricesFilterSubQb);
        $this->applyPriceFilter($filterPricesRanges, $qb);

        $this->convertDqlToSql($pricesFilterSubQb);
        $pricesFilterSubQuerySql = (string)$pricesFilterSubQb;

        $qb
            ->join('ShopCatalogBundle:Price', 'pp', Expr\Join::WITH, $qb->expr()->andX(
                $qb->expr()->eq('pp.proposalId', 'p.id'),
                $qb->expr()->in('pp.id', $pricesFilterSubQuerySql)
            ))
            ->leftJoin('ShopCatalogBundle:ContractorCurrency', 'ccu', Expr\Join::WITH, $qb->expr()->andX(
                $qb->expr()->eq('ccu.contractorId', 'pp.contractorId'),
                $qb->expr()->eq('ccu.numericCode', 'pp.currencyNumericCode')
            ))
        ;

        $qb
            ->andWhere($qb->expr()->andX(
                $qb->expr()->eq('p.categoryId', ':category_id'),
                ($manufacturerId ? $qb->expr()->eq('p.manufacturerId', ':manufacturer_id') : null)
            ))
            ->groupBy('p.id')
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
            INNER JOIN Proposal AS p ON p.categoryId = :categoryId AND p.manufacturerId = m.id
            INNER JOIN Price AS pp ON pp.proposalId = p.id AND pp.status = :priceStatus
        ';

        list($parametersExpr) = $this->createParametersExprList($categoryId, $filteredParameterValues);

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

        $qb->andWhere($qb->expr()->in('cp.filterGroup', array(
            CategoryParameter::FILTER_GROUP_MAIN,
            CategoryParameter::FILTER_GROUP_EXTRA,
        )));

        $qb
            ->addOrderBy('cp.position')
            ->addOrderBy('po.position')
            ->groupBy('po.id');

//        echo($qb);die;

        return $qb->getQuery()->getResult();

    }

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
     * @param Parameter $parameter
     * @param $categoryId
     * @param $manufacturerId
     * @param $filteredParametersValues
     * @param $filterPricesRanges
     * @param $proposalId
     * @return array
     */
    public function getParameterOptionsAmounts(Parameter $parameter, $categoryId, $manufacturerId, $filteredParametersValues, $filterPricesRanges, $proposalId = null){

        $qb = $this->getEntityManager()->createQueryBuilder();
        list($parametersExpr, $priceParametersExpr) = $this->createParametersExprList($categoryId, $filteredParametersValues);

        $queryParameters = array(
            'price_status' => Price::STATUS_ON,
            'proposal_status' => Proposal::STATUS_ON,
            'category_status' => Category::STATUS_ON,
            'parameter_id' => $parameter->getId(),
            'proposal_id' => (int)$proposalId,
            'category_id' => (int)$categoryId,
            'manufacturer_id' => (int)$manufacturerId,
            'values_amount' => count($parametersExpr),
            'price_values_amount' => count($priceParametersExpr),
        );

        $qb
            ->select(array(
                'po.id',
                'COUNT(DISTINCT p.id) AS proposalsAmount',
                'COUNT(DISTINCT pp.id) AS pricesAmount',
            ))
            ->from('ShopCatalogBundle:ParameterOption', 'po')
            ->join('ShopCatalogBundle:ParameterValue', 'popv', Expr\Join::WITH, $qb->expr()->eq('popv.optionId', 'po.id'))
        ;

        if($parameter->getIsPriceParameter()){

            $qb
                ->join('ShopCatalogBundle:Price', 'pp', Expr\Join::WITH, $qb->expr()->eq('pp.id', 'popv.priceId'))
                ->join('ShopCatalogBundle:Proposal', 'p', Expr\Join::WITH, $qb->expr()->eq('p.id', 'pp.proposalId'))
            ;

        } else {

            $qb
                ->join('ShopCatalogBundle:Proposal', 'p', Expr\Join::WITH, $qb->expr()->eq('p.id', 'popv.proposalId'))
                ->join('ShopCatalogBundle:Price', 'pp', Expr\Join::WITH, $qb->expr()->eq('pp.proposalId', 'p.id'))
            ;

        }

        $qb
            ->leftJoin('ShopCatalogBundle:ContractorCurrency', 'ccu', Expr\Join::WITH, $qb->expr()->andX(
                $qb->expr()->eq('ccu.contractorId', 'pp.contractorId'),
                $qb->expr()->eq('ccu.numericCode', 'pp.currencyNumericCode')
            ))
        ;


        $pricesFilterSubQb = $this->getEntityManager()->createQueryBuilder();
        $pricesFilterSubQb
            ->select('DISTINCT pp.id')
            ->from('ShopCatalogBundle:Price', 'pp')
            ->join('ShopCatalogBundle:Proposal', 'p', Expr\Join::WITH, $qb->expr()->eq('p.id', 'pp.proposalId'))
            ->join('ShopCatalogBundle:Category', 'c', Expr\Join::WITH, $qb->expr()->eq('c.id', 'p.categoryId'))
            ->andWhere($qb->expr()->andX(
                $qb->expr()->eq('c.id', ':category_id'),
                $qb->expr()->eq('c.status', ':category_status'),
                $qb->expr()->eq('pp.status', ':price_status'),
                $qb->expr()->eq('p.status', ':proposal_status')
            ))
        ;

        $this->applyParametersFilters($categoryId, $filteredParametersValues, $qb, $pricesFilterSubQb);
        $this->applyPriceFilter($filterPricesRanges, $qb);

        $this->convertDqlToSql($pricesFilterSubQb);
        $pricesFilterSubQuerySql = (string)$pricesFilterSubQb;

        $qb
            ->andWhere($qb->expr()->andX(
                $qb->expr()->in('pp.id', $pricesFilterSubQuerySql),
                $qb->expr()->eq('p.categoryId', ':category_id'),
                $qb->expr()->eq('po.parameterId', ':parameter_id'),
                ($proposalId ? $qb->expr()->eq('p.id', ':proposal_id') : null),
                ($manufacturerId ? $qb->expr()->eq('p.manufacturerId', ':manufacturer_id') : null)
            ))
            ->groupBy('po.id')
        ;

        $this->convertDqlToSql($qb);
        $sql = (string)$qb;

        $result = $this->getEntityManager()->getConnection()->fetchAll($sql, $queryParameters);
        $optionsAmounts = array();

        foreach($result as $optionAmounts){
            $optionsAmounts[$optionAmounts['id']] = $optionAmounts;
        }

        return $optionsAmounts;

    }

    /**
     * @param $categoryId
     * @param $filteredParametersValues
     * @return array
     */
    protected function createParametersExprList($categoryId, $filteredParametersValues)
    {

        $qb = $this->getEntityManager()->createQueryBuilder();

        $parametersExpr = array();
        $priceParametersExpr = array();

        if (is_array($filteredParametersValues)) {

            $filteredParametersValues = array_filter($filteredParametersValues);

            if ($filteredParametersValues) {

                $parameterIds = array_keys($filteredParametersValues);
                $categoryParameters = $this->getEntityManager()->getRepository('ShopCatalogBundle:CategoryParameter')->findBy(array(
                    'categoryId' => $categoryId,
                    'parameterId' => $parameterIds,
                    'filterGroup' => array(
                        CategoryParameter::FILTER_GROUP_MAIN,
                        CategoryParameter::FILTER_GROUP_EXTRA,
                    ),
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

    /**
     * @param $categoryId
     * @param $filteredParametersValues
     * @param QueryBuilder $mainQb
     * @param QueryBuilder $priceQb
     */
    public function applyParametersFilters($categoryId, $filteredParametersValues, QueryBuilder $mainQb = null, QueryBuilder $priceQb = null){

        list($parametersExpr, $priceParametersExpr) = $this->createParametersExprList($categoryId, $filteredParametersValues);

        if($parametersExpr && $mainQb){

            /**
             * @var $parameterExpr \Doctrine\ORM\Query\Expr\Andx
             */
            foreach($parametersExpr as $i => $parameterExpr){

                $alias = "pv$i";
                $comparisons = array();

                /**
                 * @var $comparison \Doctrine\ORM\Query\Expr\Comparison
                 */
                foreach($parameterExpr->getParts() as $comparison){
                    $comparisons[] = str_replace("pv", $alias, $comparison);
                }

                $mainQb->join('ShopCatalogBundle:ParameterValue', $alias, Expr\Join::WITH, $mainQb->expr()->andX(
                    $mainQb->expr()->eq("$alias.proposalId", 'p.id'),
                    call_user_func_array(array($mainQb->expr(), 'andX'), $comparisons)
                ));

            }

        }

        if($priceParametersExpr && $priceQb){

            /**
             * @var $priceParameterExpr \Doctrine\ORM\Query\Expr\Andx
             */
            foreach($priceParametersExpr as $i => $priceParameterExpr){

                $alias = "ppv$i";
                $comparisons = array();

                /**
                 * @var $comparison \Doctrine\ORM\Query\Expr\Comparison
                 */
                foreach($priceParameterExpr->getParts() as $comparison){
                    $comparisons[] = str_replace("ppv", $alias, $comparison);
                }

                $priceQb->join('ShopCatalogBundle:ParameterValue', $alias, Expr\Join::WITH, $priceQb->expr()->andX(
                    $priceQb->expr()->eq("$alias.priceId", 'pp.id'),
                    call_user_func_array(array($priceQb->expr(), 'andX'), $comparisons)
                ));

            }

        }

    }

    /**
     * @param $filterPricesRanges
     * @param QueryBuilder $qb
     */
    protected function applyPriceFilter($filterPricesRanges, QueryBuilder $qb){

        if(is_array($filterPricesRanges)){

            $filterPricesExpr = array();

            foreach($filterPricesRanges as $filterPriceRange){
                if(isset($filterPriceRange['min']) && $filterPriceRange['max']){
                    $filterPricesExpr[] = $qb->expr()->andX(
                        $qb->expr()->gte('(CASE WHEN ccu.id IS NOT NULL THEN pp.value * ccu.value ELSE pp.value END)', $filterPriceRange['min']),
                        $qb->expr()->lte('(CASE WHEN ccu.id IS NOT NULL THEN pp.value * ccu.value ELSE pp.value END)', $filterPriceRange['max'])
                    );
                }
            }

            $qb->andWhere(call_user_func_array(array($qb->expr(), 'orX'), $filterPricesExpr));

        }

    }

} 