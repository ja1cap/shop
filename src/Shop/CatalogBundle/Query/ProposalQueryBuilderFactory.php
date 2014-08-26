<?php
namespace Shop\CatalogBundle\Query;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\EntityManager;
use Shop\CatalogBundle\Entity\ProposalRepository;
use Shop\CatalogBundle\Filter\FiltersResource;

/**
 * Class ProposalQueryBuilderFactory
 * @package Shop\CatalogBundle\Query
 */
class ProposalQueryBuilderFactory {

    /**
     * @var \Shop\CatalogBundle\Entity\ProposalRepository
     */
    protected $proposalRepository;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    function __construct(EntityManager $entityManager, ProposalRepository $proposalRepository)
    {
        $this->entityManager = $entityManager;
        $this->proposalRepository = $proposalRepository;
    }

    /**
     * @param FiltersResource $filtersResource
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function createQueryBuilder(FiltersResource $filtersResource = null){

        $qb = $this->getEntityManager()->createQueryBuilder();

        //@TODO add action condition ids list
        $qb
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

        if($filtersResource){

            $this->proposalRepository->applyParametersFilters($filtersResource->getParameterFilters(), $pricesFilterSubQb);
            $this->proposalRepository->applyPriceFilter($filtersResource, $qb);

        }

        $this->proposalRepository->convertDqlToSql($pricesFilterSubQb);
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
                ($filtersResource && $filtersResource->getManufacturerFilter()->getValue() ? $qb->expr()->in('p.manufacturerId', $filtersResource->getManufacturerFilter()->getValue()) : null)
            ))
        ;

        return $qb;

    }

    /**
     * @return EntityManager
     */
    private function getEntityManager(){
        return $this->entityManager;
    }

} 