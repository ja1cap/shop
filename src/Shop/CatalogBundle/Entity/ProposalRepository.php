<?php
namespace Shop\CatalogBundle\Entity;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\QueryBuilder;
use Shop\CatalogBundle\Filter\FiltersResource;
use Shop\CatalogBundle\Filter\OptionsFilter\OptionsFilter;
use Shop\CatalogBundle\Filter\OptionsFilter\OptionsFilterInterface;
use Shop\CatalogBundle\Filter\SliderFilter\SliderFilter;
use Shop\CatalogBundle\Query\ProposalQueryBuilderFactory;
use Shop\DiscountBundle\Entity\ActionInterface;
use Weasty\Doctrine\Entity\AbstractRepository;

/**
 * Class ProposalRepository
 * @package Shop\CatalogBundle\Entity
 */
class ProposalRepository extends AbstractRepository {

    /**
     * @var \Weasty\Doctrine\Cache\Collection\CacheCollectionManager
     */
    protected $cacheCollectionManager;

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
    public function getPriceRange($categoryId){

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
     * @param FiltersResource $filtersResource
     * @return array
     */
    public function getPriceIntervalsData($categoryId, $proposalId = null, FiltersResource $filtersResource){

        $priceRange = $this->getPriceRange($categoryId);
        $minPrice = floatval($priceRange['minPrice']);
        $maxPrice = floatval($priceRange['maxPrice']);

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

            $priceRange = $i * $priceInterval;

            if($priceRange >= $minPriceStep && in_array($priceRange, $existingPriceSteps)){

                $min = $priceRange;
                $max = $priceRange + $priceInterval;

                if($min == 0){

                    $min = $minPrice;

                } elseif($max >= $maxPrice){

                    $max = $maxPrice;

                }

                $priceIntervals[$priceRange] = array(
                    'min' => $min,
                    'max' => $max,
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
                ($filtersResource->getManufacturerFilter()->getValue() ? $qb->expr()->in('p.manufacturerId', $filtersResource->getManufacturerFilter()->getValue()) : null)
            ))
            ->groupBy('priceStep')
            ->orderBy('priceStep', 'ASC')
        ;

        $this->convertDqlToSql($qb);
        $sql = (string)$qb;

        $priceStepsAmounts = $this->getEntityManager()->getConnection()->executeQuery($sql, $queryParameters)->fetchAll(\PDO::FETCH_KEY_PAIR);
        foreach($priceStepsAmounts as $priceRange => $pricesAmount){

            if(isset($priceIntervals[$priceRange])){
                $priceIntervals[$priceRange]['pricesAmount'] = (int)$pricesAmount;
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

    /**
     * @param FiltersResource $filtersResource
     * @return mixed
     */
    public function findProposalPrice(FiltersResource $filtersResource){

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
            'proposal_id' => $filtersResource->getProposalId(),
            'proposal_status' => Proposal::STATUS_ON,
            'category_id' => $filtersResource->getCategoryId(),
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

//        $result = $query->getResult();
        $result = current($this->findProposalsByFilters($filtersResource, 1, 1));

        if($result){

            if(isset($result['priceId']) && $result['priceId']){

                $priceCollection = $this->getCacheCollectionManager()->getCollection('ShopCatalogBundle:Price');
                $result['price'] = $priceCollection->get($result['priceId']);

            }

        }

        return $result;


    }

    /**
     * @param $categoryId
     * @param null $contractorsIds
     * @param null $manufacturersIds
     * @return array
     */
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
     * @param FiltersResource $filtersResource
     * @param null $page
     * @param null $perPage
     * @return array
     */
    public function findProposalsByFilters(FiltersResource $filtersResource, $page = null, $perPage = null){

        //@TODO cache sql query by FiltersResource::getCacheId()
        $useCacheCollection = true;

        $qbFactory = new ProposalQueryBuilderFactory($this->getEntityManager(), $this);
        $qb = $qbFactory->createQueryBuilder($filtersResource);

        $queryParameters = array(
            'price_status' => Price::STATUS_ON,
            'proposal_status' => Proposal::STATUS_ON,
            'category_status' => Category::STATUS_ON,
        );

        $select = [
            ($useCacheCollection ? 'p.id as proposalId' : 'p.*'),
            'MIN((CASE WHEN ccu.id IS NOT NULL THEN pp.value * ccu.value ELSE pp.value END)) AS price',
            'MAX((CASE WHEN ccu.id IS NOT NULL THEN pp.value * ccu.value ELSE pp.value END)) AS maxPrice',
            'pp.id as priceId',
            'GROUP_CONCAT(DISTINCT ac.id) as actionConditionIds',
            'action_p.id IS NOT NULL as hasAction',
            'discount_p.id IS NOT NULL as hasDiscount',
        ];

        $qb
            ->select(array_filter($select))
            ->leftJoin('ShopDiscountBundle:ActionConditionProposal', 'acp', Expr\Join::WITH, $qb->expr()->eq('acp.proposalId', 'p.id'))
            ->leftJoin('ShopDiscountBundle:ActionConditionCategory', 'acc', Expr\Join::WITH, $qb->expr()->eq('acc.categoryId', 'p.categoryId'))
            ->leftJoin('ShopDiscountBundle:Action', 'a', Expr\Join::WITH, $qb->expr()->andX(
                $qb->expr()->eq('a.status', ActionInterface::STATUS_ON)
            ))
            ->leftJoin('ShopDiscountBundle:ActionCondition', 'ac', Expr\Join::WITH, $qb->expr()->andX(
                $qb->expr()->eq('ac.actionId', 'a.id'),
                $qb->expr()->orX(
                    $qb->expr()->eq('ac.id', 'acp.conditionId'),
                    $qb->expr()->eq('ac.id', 'acc.conditionId')
                )
            ))
            ->leftJoin('ShopCatalogBundle:Proposal', 'action_p', Expr\Join::WITH, $qb->expr()->andX(
                $qb->expr()->eq('action_p.id', 'p.id'),
                $qb->expr()->orX(
                    $qb->expr()->eq('action_p.id', 'acp.proposalId'),
                    $qb->expr()->eq('action_p.categoryId', 'acc.categoryId')
                )
            ))
            ->leftJoin('ShopDiscountBundle:ActionConditionDiscountProposal', 'acdp', Expr\Join::WITH, $qb->expr()->andX(
                $qb->expr()->eq('acdp.conditionId', 'ac.id'),
                $qb->expr()->eq('acdp.proposalId', 'p.id')
            ))
            ->leftJoin('ShopDiscountBundle:ActionConditionDiscountProposal', 'all_acdp', Expr\Join::WITH, $qb->expr()->andX(
                $qb->expr()->eq('all_acdp.conditionId', 'ac.id')
            ))
            ->leftJoin('ShopDiscountBundle:ActionConditionDiscountCategory', 'acdc', Expr\Join::WITH, $qb->expr()->andX(
                $qb->expr()->eq('acdc.conditionId', 'ac.id'),
                $qb->expr()->eq('acdc.categoryId', 'p.categoryId')
            ))
            ->leftJoin('ShopDiscountBundle:ActionConditionDiscountCategory', 'all_acdc', Expr\Join::WITH, $qb->expr()->andX(
                $qb->expr()->eq('all_acdc.conditionId', 'ac.id')
            ))
            ->leftJoin('ShopCatalogBundle:Proposal', 'discount_p', Expr\Join::WITH, $qb->expr()->andX(
                $qb->expr()->eq('discount_p.id', 'p.id'),
                $qb->expr()->orX(
                    $qb->expr()->orX(
                        $qb->expr()->andX(
                            $qb->expr()->isNotNull('acdp.id'),
                            $qb->expr()->eq('discount_p.id', 'acdp.proposalId')
                        ),
                        $qb->expr()->andX(
                            $qb->expr()->isNull('acdp.id'),
                            $qb->expr()->isNull('all_acdp.id'),
                            $qb->expr()->eq('discount_p.id', 'acp.proposalId')
                        )
                    ),
                    $qb->expr()->orX(
                        $qb->expr()->andX(
                            $qb->expr()->isNotNull('acdc.id'),
                            $qb->expr()->eq('discount_p.categoryId', 'acdc.categoryId')
                        ),
                        $qb->expr()->andX(
                            $qb->expr()->isNull('acdc.id'),
                            $qb->expr()->isNull('all_acdp.id'),
                            $qb->expr()->isNull('all_acdc.id'),
                            $qb->expr()->eq('discount_p.categoryId', 'acc.categoryId')
                        )
                    )
                )
            ))
            ->groupBy('p.id')
            ->addOrderBy('price')
        ;

        if($filtersResource){

            $qb
                ->andWhere(
                    ($filtersResource->getIsNewFilter() && $filtersResource->getIsNewFilter()->getValue() ? $qb->expr()->eq('p.isNew', 1) : null),
                    ($filtersResource->getIsBestsellerFilter() && $filtersResource->getIsBestsellerFilter()->getValue() ? $qb->expr()->eq('p.isBestseller', 1) : null),
                    ($filtersResource->getHasActionFilter() && $filtersResource->getHasActionFilter()->getValue() ? $qb->expr()->isNotNull('action_p.id') : null),
                    ($filtersResource->getHasDiscountFilter() && $filtersResource->getHasDiscountFilter()->getValue() ? $qb->expr()->isNotNull('discount_p.id') : null)
                )
            ;

        }


        $this->convertDqlToSql($qb);
        $sql = (string)$qb;

        if($page && $perPage){
            $sql .= ' LIMIT ' . ($page > 1 ? (int)$page . ',' : '') . $perPage;
        }

        if($useCacheCollection){
            $rsm = new ResultSetMapping();
            $rsm->addScalarResult('proposalId', 'proposalId', 'integer');
        } else {
            $rsm = $this->createResultSetMappingFromMetadata('ShopCatalogBundle:Proposal', 'p', 'proposal');
        }

        $rsm->addScalarResult('priceId', 'priceId', 'integer');
        $rsm->addScalarResult('price', 'price', 'float');
        $rsm->addScalarResult('maxPrice', 'maxPrice', 'float');
        $rsm->addScalarResult('actionConditionIds', 'actionConditionIds', 'simple_array');
        $rsm->addScalarResult('hasAction', 'hasAction', 'boolean');
        $rsm->addScalarResult('hasDiscount', 'hasDiscount', 'boolean');

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $query->setParameters($queryParameters);

        $result = $query->getResult();

        if($useCacheCollection){

            $proposalCollection = $this->getCacheCollectionManager()->getCollection('ShopCatalogBundle:Proposal');

            foreach($result as $i => $proposalData){

                $proposal = $proposalCollection->get($proposalData['proposalId']);
                if($proposal){
                    $result[$i]['proposal'] = $proposal;
                } else {
                    unset($result[$i]);
                    continue;
                }

            }

        }

//        var_dump($result);
//        die;

//        foreach($queryParameters as $key => $value){
//            $sql = str_replace(':' . $key, $value, $sql);
//        }
//        echo($sql);
//        echo("<br/>");
//        die;

        return $result;

    }

    /**
     * @param null FiltersResource $filtersResource
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countProposals(FiltersResource $filtersResource = null){

        $qbFactory = new ProposalQueryBuilderFactory($this->getEntityManager(), $this);
        $qb = $qbFactory->createQueryBuilder($filtersResource);

        $queryParameters = array(
            'price_status' => Price::STATUS_ON,
            'proposal_status' => Proposal::STATUS_ON,
            'category_status' => Category::STATUS_ON,
            'category_id' => (int)$filtersResource->getCategoryId(),
        );

        $qb
            ->select(array(
                'COUNT(DISTINCT p.id) as filtered',
                'COUNT(DISTINCT new_p.id) as new',
                'COUNT(DISTINCT best_p.id) as best',
                'COUNT(DISTINCT action_p.id) as action',
                'COUNT(DISTINCT discount_p.id) as discount',
            ))
            ->leftJoin('ShopCatalogBundle:Proposal', 'new_p', Expr\Join::WITH, $qb->expr()->andX(
                $qb->expr()->eq('new_p.id', 'p.id'),
                $qb->expr()->eq('new_p.isNew', 1)
            ))
            ->leftJoin('ShopCatalogBundle:Proposal', 'best_p', Expr\Join::WITH, $qb->expr()->andX(
                $qb->expr()->eq('best_p.id', 'p.id'),
                $qb->expr()->eq('best_p.isBestseller', 1)
            ))
            ->leftJoin('ShopDiscountBundle:ActionConditionProposal', 'acp', Expr\Join::WITH, $qb->expr()->eq('acp.proposalId', 'p.id'))
            ->leftJoin('ShopDiscountBundle:ActionConditionCategory', 'acc', Expr\Join::WITH, $qb->expr()->eq('acc.categoryId', 'p.categoryId'))
            ->leftJoin('ShopDiscountBundle:Action', 'a', Expr\Join::WITH, $qb->expr()->andX(
                $qb->expr()->eq('a.status', ActionInterface::STATUS_ON)
            ))
            ->leftJoin('ShopDiscountBundle:ActionCondition', 'ac', Expr\Join::WITH, $qb->expr()->andX(
                $qb->expr()->eq('ac.actionId', 'a.id'),
                $qb->expr()->orX(
                    $qb->expr()->eq('ac.id', 'acp.conditionId'),
                    $qb->expr()->eq('ac.id', 'acc.conditionId')
                )
            ))
            ->leftJoin('ShopCatalogBundle:Proposal', 'action_p', Expr\Join::WITH, $qb->expr()->andX(
                $qb->expr()->eq('action_p.id', 'p.id'),
                $qb->expr()->orX(
                    $qb->expr()->eq('action_p.id', 'acp.proposalId'),
                    $qb->expr()->eq('action_p.categoryId', 'acc.categoryId')
                )
            ))
            ->leftJoin('ShopDiscountBundle:ActionConditionDiscountProposal', 'acdp', Expr\Join::WITH, $qb->expr()->andX(
                $qb->expr()->eq('acdp.conditionId', 'ac.id'),
                $qb->expr()->eq('acdp.proposalId', 'p.id')
            ))
            ->leftJoin('ShopDiscountBundle:ActionConditionDiscountProposal', 'all_acdp', Expr\Join::WITH, $qb->expr()->andX(
                $qb->expr()->eq('all_acdp.conditionId', 'ac.id')
            ))
            ->leftJoin('ShopDiscountBundle:ActionConditionDiscountCategory', 'acdc', Expr\Join::WITH, $qb->expr()->andX(
                $qb->expr()->eq('acdc.conditionId', 'ac.id'),
                $qb->expr()->eq('acdc.categoryId', 'p.categoryId')
            ))
            ->leftJoin('ShopDiscountBundle:ActionConditionDiscountCategory', 'all_acdc', Expr\Join::WITH, $qb->expr()->andX(
                $qb->expr()->eq('all_acdc.conditionId', 'ac.id')
            ))
            ->leftJoin('ShopCatalogBundle:Proposal', 'discount_p', Expr\Join::WITH, $qb->expr()->andX(
                $qb->expr()->eq('discount_p.id', 'p.id'),
                $qb->expr()->orX(
                    $qb->expr()->orX(
                        $qb->expr()->andX(
                            $qb->expr()->isNotNull('acdp.id'),
                            $qb->expr()->eq('discount_p.id', 'acdp.proposalId')
                        ),
                        $qb->expr()->andX(
                            $qb->expr()->isNull('acdp.id'),
                            $qb->expr()->isNull('all_acdp.id'),
                            $qb->expr()->eq('discount_p.id', 'acp.proposalId')
                        )
                    ),
                    $qb->expr()->orX(
                        $qb->expr()->andX(
                            $qb->expr()->isNotNull('acdc.id'),
                            $qb->expr()->eq('discount_p.categoryId', 'acdc.categoryId')
                        ),
                        $qb->expr()->andX(
                            $qb->expr()->isNull('acdc.id'),
                            $qb->expr()->isNull('all_acdp.id'),
                            $qb->expr()->isNull('all_acdc.id'),
                            $qb->expr()->eq('discount_p.categoryId', 'acc.categoryId')
                        )
                    )
                )
            ))
        ;

        $this->convertDqlToSql($qb);
        $sql = (string)$qb;

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('filtered', 'filtered');
        $rsm->addScalarResult('new', 'new');
        $rsm->addScalarResult('best', 'best');
        $rsm->addScalarResult('action', 'action');
        $rsm->addScalarResult('discount', 'discount');
        $rsm->addScalarResult('actionConditions', 'actionConditions');

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $query->setParameters($queryParameters);

        $result = $query->getSingleResult();

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

        $qb->andWhere(
            $qb->expr()->orX(
                'FIND_IN_SET(' . OptionsFilterInterface::GROUP_MAIN . ', cp.filterGroups) > 0',
                'FIND_IN_SET(' . OptionsFilterInterface::GROUP_EXTRA . ', cp.filterGroups) > 0'
            )
        );

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

    /**
     * @param $parameterId
     * @param $categoryId
     * @param null $proposalId
     * @param FiltersResource $filtersResource
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getParameterOptionsPricesAmount($parameterId, $categoryId, $proposalId = null, FiltersResource $filtersResource){

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
                ($filtersResource->getManufacturerFilter()->getValue() ? $qb->expr()->in('p.manufacturerId', $filtersResource->getManufacturerFilter()->getValue()) : null)
            ))
            ->groupBy('po.id')
        ;

        if($filtersResource){

            $qb
                ->andWhere(
                    ($filtersResource->getIsNewFilter() && $filtersResource->getIsNewFilter()->getValue() ? $qb->expr()->eq('p.isNew', 1) : null),
                    ($filtersResource->getIsBestsellerFilter() && $filtersResource->getIsBestsellerFilter()->getValue() ? $qb->expr()->eq('p.isBestseller', 1) : null)
                    //@TODO
                    //($filtersResource->getHasActionFilter() && $filtersResource->getHasActionFilter()->getValue() ? $qb->expr()->isNotNull('action_p.id') : null),
                    //($filtersResource->getHasDiscountFilter() && $filtersResource->getHasDiscountFilter()->getValue() ? $qb->expr()->isNotNull('discount_p.id') : null)
                )
            ;

        }


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
         * @var \Shop\CatalogBundle\Filter\ParameterFilter\ParameterFilter $parameterFilter
         */
        foreach($parameterFilters as $parameterFilter){

            if($parameterFilter->getValue()){

                $expressions[] = $qb->expr()->andX(
                    $qb->expr()->eq('ppv.parameterId', $parameterFilter->getParameterId()),
                    $qb->expr()->in('ppv.optionId', $parameterFilter->getValue())
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
     * @param FiltersResource $filtersResource
     * @param QueryBuilder $qb
     */
    public function applyPriceFilter(FiltersResource $filtersResource, QueryBuilder $qb){

        $filter = $filtersResource->getPriceRangeFilter();

        if($filter instanceof OptionsFilter){

            $filterPricesExpr = array();

            foreach($filter->getValue() as $optionId){

                /**
                 * @var \Shop\CatalogBundle\Filter\PriceRangeFilter\PriceRangeFilterOption $filterOption
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

        } elseif($filter instanceof SliderFilter){

            $value = $filter->getValue();
            $filterPricesExpr = array();

            $min = $value['min'];
            if($min != $filter->getMinValue()){
                $filterPricesExpr[] = $qb->expr()->gte('(CASE WHEN ccu.id IS NOT NULL THEN pp.value * ccu.value ELSE pp.value END)', $min);
            }

            $max = $value['max'];
            if($max != $filter->getMaxValue()){
                $filterPricesExpr[] = $qb->expr()->lte('(CASE WHEN ccu.id IS NOT NULL THEN pp.value * ccu.value ELSE pp.value END)', $max);
            }

            if($filterPricesExpr){
                $qb->andWhere(call_user_func_array(array($qb->expr(), 'andX'), $filterPricesExpr));
            }

        }

    }

    /**
     * @return \Weasty\Doctrine\Cache\Collection\CacheCollectionManager
     */
    public function getCacheCollectionManager()
    {
        return $this->cacheCollectionManager;
    }

    /**
     * @param \Weasty\Doctrine\Cache\Collection\CacheCollectionManager $cacheCollectionManager
     */
    public function setCacheCollectionManager($cacheCollectionManager)
    {
        $this->cacheCollectionManager = $cacheCollectionManager;
    }

} 