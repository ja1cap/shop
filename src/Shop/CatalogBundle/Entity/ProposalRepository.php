<?php
namespace Shop\CatalogBundle\Entity;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Shop\CatalogBundle\Filter\CategoryFiltersResource;
use Shop\CatalogBundle\Filter\FilterInterface;
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
     * @param null $proposalId
     * @param CategoryFiltersResource $filtersResource
     * @return array
     */
    public function getPriceIntervalsData($categoryId, $proposalId = null, CategoryFiltersResource $filtersResource){

        $priceStep = $this->getProposalPriceRange($categoryId);
        $minPrice = floatval($priceStep['minPrice']);
        $maxPrice = floatval($priceStep['maxPrice']);

        $averagePriceLength = strlen(($minPrice + $maxPrice)/2);

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
            'price_exponent' => $priceExponent,
        );

        $priceInterval = floatval(pow(10, $priceExponent));
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
                    'min' => $priceStep,
                    'max' => $priceStep + $priceInterval,
                    'pricesAmount' => 0,
                );

            }

        }

        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb
            ->select(array(
                'FLOOR((CASE WHEN ccu.id IS NOT NULL THEN pp.value * ccu.value ELSE pp.value END) / POW(10, :price_exponent)) * POW(10, :price_exponent) AS priceStep',
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

        $this->applyParametersFilters($filtersResource->getParameterFilters(), $pricesFilterSubQb);

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
                ($filtersResource->getManufacturerFilter()->getFilteredOptionIds() ? $qb->expr()->in('p.manufacturerId', $filtersResource->getManufacturerFilter()->getFilteredOptionIds()) : null)
            ))
            ->groupBy('priceStep')
            ->orderBy('priceStep', 'ASC')
        ;

        $this->convertDqlToSql($qb);
        $sql = (string)$qb;

        $priceStepsAmounts = $this->getEntityManager()->getConnection()->executeQuery($sql, $queryParameters)->fetchAll(\PDO::FETCH_KEY_PAIR);
        foreach($priceStepsAmounts as $priceStep => $pricesAmount){

            if(isset($priceIntervals[$priceStep])){
                $priceIntervals[$priceStep]['pricesAmount'] = (int)$pricesAmount;
            }

        }

        $result = array(
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
            'interval' => $priceInterval,
            'intervals' => $priceIntervals,
        );

        return $result;

    }

    public function findProposalPrice($categoryId, $proposalId, CategoryFiltersResource $filtersResource){

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

        $this->applyParametersFilters($filtersResource->getParameterFilters(), $pricesFilterSubQb);
        $this->applyPriceFilter($filtersResource, $pricesFilterSubQb);
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

    public function findProposalsByParameters($categoryId, CategoryFiltersResource $filtersResource, $page = null, $perPage = null){

        $qb = $this->getEntityManager()->createQueryBuilder();

        $queryParameters = array(
            'price_status' => Price::STATUS_ON,
            'proposal_status' => Proposal::STATUS_ON,
            'category_status' => Category::STATUS_ON,
            'category_id' => (int)$categoryId,
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

        $this->applyParametersFilters($filtersResource->getParameterFilters(), $pricesFilterSubQb);
        $this->applyPriceFilter($filtersResource, $qb);

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
                ($filtersResource->getManufacturerFilter()->getFilteredOptionIds() ? $qb->expr()->in('p.manufacturerId', $filtersResource->getManufacturerFilter()->getFilteredOptionIds()) : null)
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
     * @return array
     */
    public function findCategoryManufacturers($categoryId){

        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb
            ->select('m')
            ->from('ShopCatalogBundle:Manufacturer', 'm')
            ->join('ShopCatalogBundle:Proposal', 'p', Expr\Join::INNER_JOIN, $qb->expr()->andX(
                $qb->expr()->eq('p.categoryId', (int)$categoryId),
                $qb->expr()->eq('p.manufacturerId', 'm.id'),
                $qb->expr()->eq('p.status', Proposal::STATUS_ON)
            ))
            ->join('ShopCatalogBundle:Price', 'pp', Expr\Join::INNER_JOIN, $qb->expr()->andX(
                $qb->expr()->eq('pp.proposalId', 'p.id'),
                $qb->expr()->eq('pp.status', Price::STATUS_ON)
            ))
            ->orderBy('m.name', 'ASC')
        ;

        $query = $qb->getQuery();

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
            ->join('ShopCatalogBundle:Price', 'pp', Expr\Join::WITH, $qb->expr()->andX(
                $qb->expr()->eq('pp.status', Price::STATUS_ON),
                $qb->expr()->eq('pp.id', 'pv.priceId')
            ))
            ->join('ShopCatalogBundle:Proposal', 'p', Expr\Join::WITH, $qb->expr()->andX(
                $qb->expr()->eq('p.id', 'pp.proposalId'),
                $qb->expr()->eq('p.status', Proposal::STATUS_ON),
                $qb->expr()->eq('p.categoryId', $categoryId)
            ))
        ;

        $qb->andWhere($qb->expr()->in('cp.filterGroup', array(
            FilterInterface::GROUP_MAIN,
            FilterInterface::GROUP_EXTRA,
        )));

        $qb
            ->addOrderBy('cp.position')
            ->addOrderBy('po.position')
            ->groupBy('po.id');

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

    public function getParameterOptionsPricesAmount($parameterId, $categoryId, $proposalId = null, CategoryFiltersResource $filtersResource){

        $qb = $this->getEntityManager()->createQueryBuilder();

        $queryParameters = array(
            'price_status' => Price::STATUS_ON,
            'proposal_status' => Proposal::STATUS_ON,
            'category_status' => Category::STATUS_ON,
            'parameter_id' => (int)$parameterId,
            'proposal_id' => (int)$proposalId,
            'category_id' => (int)$categoryId,
        );

        $qb
            ->select(array(
                'po.id',
                'COUNT(DISTINCT pp.id) AS pricesAmount',
            ))
            ->from('ShopCatalogBundle:ParameterOption', 'po')
            ->join('ShopCatalogBundle:ParameterValue', 'popv', Expr\Join::WITH, $qb->expr()->eq('popv.optionId', 'po.id'))
        ;

        $qb
            ->join('ShopCatalogBundle:Price', 'pp', Expr\Join::WITH, $qb->expr()->eq('pp.id', 'popv.priceId'))
            ->join('ShopCatalogBundle:Proposal', 'p', Expr\Join::WITH, $qb->expr()->eq('p.id', 'pp.proposalId'))
        ;

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

        $parameterFilters = $filtersResource->getParameterFilters();
        if(isset($parameterFilters[$parameterId])){
            unset($parameterFilters[$parameterId]);
        }

        $this->applyParametersFilters($parameterFilters, $pricesFilterSubQb);
        $this->applyPriceFilter($filtersResource, $qb);

        $this->convertDqlToSql($pricesFilterSubQb);
        $pricesFilterSubQuerySql = (string)$pricesFilterSubQb;

        $qb
            ->andWhere($qb->expr()->andX(
                $qb->expr()->in('pp.id', $pricesFilterSubQuerySql),
                $qb->expr()->eq('p.categoryId', ':category_id'),
                $qb->expr()->eq('po.parameterId', ':parameter_id'),
                ($proposalId ? $qb->expr()->eq('p.id', ':proposal_id') : null),
                ($filtersResource->getManufacturerFilter()->getFilteredOptionIds() ? $qb->expr()->in('p.manufacturerId', $filtersResource->getManufacturerFilter()->getFilteredOptionIds()) : null)
            ))
            ->groupBy('po.id')
        ;

        $this->convertDqlToSql($qb);
        $sql = (string)$qb;

        return $this->getEntityManager()->getConnection()->executeQuery($sql, $queryParameters)->fetchAll(\PDO::FETCH_KEY_PAIR);

    }

    /**
     * @param array $parameterFilters
     * @return array
     */
    protected function createParametersExpressions(array $parameterFilters)
    {

        $qb = $this->getEntityManager()->createQueryBuilder();

        $expressions = array();

        /**
         * @var \Shop\CatalogBundle\Filter\ParameterFilter $parameterFilter
         */
        foreach($parameterFilters as $parameterFilter){

            if($parameterFilter->getFilteredOptionIds()){

                $expressions[] = $qb->expr()->andX(
                    $qb->expr()->eq('ppv.parameterId', $parameterFilter->getParameterId()),
                    $qb->expr()->in('ppv.optionId', $parameterFilter->getFilteredOptionIds())
                );

            }

        }

        return $expressions;

    }

    /**
     * @param array $parameterFilters
     * @param QueryBuilder $qb
     */
    public function applyParametersFilters(array $parameterFilters, QueryBuilder $qb = null){

        $expressions = $this->createParametersExpressions($parameterFilters);

        if($expressions && $qb){

            /**
             * @var $priceParameterExpr \Doctrine\ORM\Query\Expr\Andx
             */
            foreach($expressions as $i => $priceParameterExpr){

                $alias = "ppv$i";
                $comparisons = array();

                /**
                 * @var $comparison \Doctrine\ORM\Query\Expr\Comparison
                 */
                foreach($priceParameterExpr->getParts() as $comparison){
                    $comparisons[] = str_replace("ppv", $alias, $comparison);
                }

                $qb->join('ShopCatalogBundle:ParameterValue', $alias, Expr\Join::WITH, $qb->expr()->andX(
                    $qb->expr()->eq("$alias.priceId", 'pp.id'),
                    call_user_func_array(array($qb->expr(), 'andX'), $comparisons)
                ));

            }

        }

    }

    /**
     * @param CategoryFiltersResource $filtersResource
     * @param QueryBuilder $qb
     */
    protected function applyPriceFilter(CategoryFiltersResource $filtersResource, QueryBuilder $qb){

        $filter = $filtersResource->getPriceRangeFilter();
        $filterPricesExpr = array();

        foreach($filter->getFilteredOptionIds() as $optionId){

            /**
             * @var \Shop\CatalogBundle\Filter\PriceRangeFilterOption $filterOption
             */
            $filterOption = $filter->getOption($optionId);
            if($filterOption){

                $filterPricesExpr[] = $qb->expr()->andX(
                    $qb->expr()->gte('(CASE WHEN ccu.id IS NOT NULL THEN pp.value * ccu.value ELSE pp.value END)', $filterOption->getMin()),
                    $qb->expr()->lte('(CASE WHEN ccu.id IS NOT NULL THEN pp.value * ccu.value ELSE pp.value END)', $filterOption->getMax())
                );

            }

        }

        $qb->andWhere(call_user_func_array(array($qb->expr(), 'orX'), $filterPricesExpr));

    }

} 