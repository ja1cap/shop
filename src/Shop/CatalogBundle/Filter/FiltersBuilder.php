<?php
namespace Shop\CatalogBundle\Filter;
use Shop\CatalogBundle\Entity\Category;
use Shop\CatalogBundle\Entity\CategoryFilters;
use Shop\CatalogBundle\Entity\Proposal;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * Class FiltersBuilder
 * @package Shop\CatalogBundle\Filter
 */
class FiltersBuilder {

    const PARAMETER_VALUES_FILTER_COOKIE_NAME = 'parameters';

    /**
     * @var \Doctrine\Common\Cache\Cache
     */
    protected $cache;

    /**
     * @var \Weasty\MoneyBundle\Twig\AbstractMoneyExtension
     */
    protected $moneyExtension;

    /**
     * @var \Shop\CatalogBundle\Entity\ProposalRepository
     */
    protected $proposalRepository;

    function __construct($proposalRepository, $moneyExtension, $cache)
    {
        $this->proposalRepository = $proposalRepository;
        $this->moneyExtension = $moneyExtension;
        $this->cache = $cache;
    }

    /**
     * @param Category $category
     * @param Proposal $proposal
     * @param array $manufacturerIds
     * @param array $parametersFilteredOptionIds
     * @param array $priceRangeSteps
     * @return FiltersResource
     */
    public function build(Category $category, Proposal $proposal = null, $manufacturerIds = array(), $parametersFilteredOptionIds = array(), $priceRangeSteps = array()){

        $cacheId = 'category_filters_resource_' . md5(serialize(array(
            'categoryId' => $category->getId(),
            'proposal' => ($proposal ? $proposal->getId() : null),
            'manufacturer' => $manufacturerIds,
            self::PARAMETER_VALUES_FILTER_COOKIE_NAME => $parametersFilteredOptionIds,
            'prices' => $priceRangeSteps,
        )));

        $filtersResource = $this->cache->fetch($cacheId);
        if(true){

            $filtersResource = new FiltersResource();

            $manufacturerFilter = $this->buildManufacturerFilter($category, $manufacturerIds);
            $filtersResource->setManufacturerFilter($manufacturerFilter);

            $this->buildParameterFilters($category, $parametersFilteredOptionIds, $filtersResource);

            $priceRangeFilter = $this->buildPriceRangerFilter($category, $priceRangeSteps, $filtersResource);
            $filtersResource->setPriceRangeFilter($priceRangeFilter);

            foreach($filtersResource->getParameterFilters() as $parameterFilter){

                $parameterOptionsPricesAmount = $this->getProposalRepository()->getParameterOptionsPricesAmount($parameterFilter->getParameterId(), $category->getId(), ($proposal ? $proposal->getId() : null), $filtersResource);
                foreach($parameterOptionsPricesAmount as $optionId => $pricesAmount){
                    $option = $parameterFilter->getOption($optionId);
                    if($option){
                        $option->setPricesAmount($pricesAmount);
                    }
                }

            }

            $this->cache->save($cacheId, $filtersResource);

        }

        return $filtersResource;

    }

    /**
     * @param CategoryFilters $categoryFilters
     * @return FiltersResource
     */
    public function buildFromCategoryFilters(CategoryFilters $categoryFilters){

        return $this->build(
            $categoryFilters->getCategory(),
            null,
            $categoryFilters->getManufacturerFilter(),
            $categoryFilters->getParameterFilters(),
            $categoryFilters->getPriceRangeFilter()
        );

    }

    /**
     * @param Category $category
     * @param Proposal $proposal
     * @param Request $request
     * @return mixed|FiltersResource
     */
    public function buildFromRequest(Category $category, Proposal $proposal = null, Request $request){

        $manufacturer = $request->get('manufacturer', $request->cookies->get('manufacturer'));
        $parametersFilteredOptionIds = $this->getParametersFilteredOptionIds($request);
        $priceRangeSteps = $this->getPriceRageSteps($request, $category);

        return $this->build($category, $proposal, $manufacturer, $parametersFilteredOptionIds, $priceRangeSteps);

    }

    /**
     * @param Category $category
     * @param Request $request
     * @param Response $response
     * @return $this
     */
    public function setFiltersCookies(Category $category, Request $request, Response $response){

        if($request->query->has('parameters')){
            $response->headers->setCookie(new Cookie(self::PARAMETER_VALUES_FILTER_COOKIE_NAME, json_encode($request->query->get('parameters'))));
        }

        if($request->query->has('manufacturer')){
            $response->headers->setCookie(new Cookie('manufacturer', $request->query->get('manufacturer')));
        }

        if($request->query->has('prices')){
            $response->headers->setCookie(new Cookie('prices' . $category->getId(), json_encode($request->query->get('prices'))));
        }

        return $this;

    }

    /**
     * @return \Shop\CatalogBundle\Entity\ProposalRepository
     */
    protected function getProposalRepository()
    {
        return $this->proposalRepository;
    }

    /**
     * @param Category $category
     * @param int[] $manufacturerIds
     * @return FilterInterface[]
     */
    public function buildManufacturerFilter(Category $category, $manufacturerIds = array())
    {

        if ($manufacturerIds && !is_array($manufacturerIds)) {
            $manufacturerIds = array(
                (int)$manufacturerIds
            );
        } elseif(!$manufacturerIds) {
            $manufacturerIds = array();
        }

        $filter = new Filter();
        $filter->setGroups(array(
            FilterInterface::GROUP_MAIN,
        ));
        $filteredOptionIds = array();

        /**
         * @var \Shop\CatalogBundle\Entity\Manufacturer $manufacturer
         */
        $categoryManufacturers = $this->getProposalRepository()->findCategoryManufacturers($category->getId());

        foreach ($categoryManufacturers as $manufacturer) {

            $filterOption = new FilterOption();
            $filterOption
                ->setId($manufacturer->getId())
                ->setName($manufacturer->getName())
            ;

            if(in_array($filterOption->getId(), $manufacturerIds)){

                $filteredOptionIds[] = $filterOption->getId();
                $filterOption->setIsSelected();

            }

            $filter->addOption($filterOption);

        }

        $filter->setFilteredOptionIds($filteredOptionIds);

        return $filter;

    }

    /**
     * @param Request $request
     * @return array
     */
    protected function getParametersFilteredOptionIds(Request $request)
    {
        $cookieName = self::PARAMETER_VALUES_FILTER_COOKIE_NAME;
        $cookie = $request->cookies->get($cookieName);

        if ($cookie) {

            $cookie = json_decode($request->cookies->get($cookieName), true);
            if (!is_array($cookie)) {
                $cookie = null;
            }

        }

        $optionIds = $request->get('parameters', $cookie);

        if (is_array($optionIds)) {
            $optionIds = array_filter($optionIds);
        } else {
            $optionIds = array();
        }

        return $optionIds;

    }

    /**
     * @param Category $category
     * @param array $parametersFilteredOptionIds
     * @param FiltersResource $filtersResource
     */
    public function buildParameterFilters(Category $category, array $parametersFilteredOptionIds, FiltersResource $filtersResource)
    {

        $parametersOptions = $this->getProposalRepository()->findCategoryParametersOptions($category->getId());

        /**
         * @var \Shop\CatalogBundle\Entity\ParameterOption $parameterOption
         */
        foreach ($parametersOptions as $parameterOption) {

            if (!$parameterFilter = $filtersResource->getParameterFilter($parameterOption->getParameterId())) {

                $parameterFilter = new ParameterFilter();
                $parameterFilter
                    ->setParameterId($parameterOption->getParameterId())
                ;

                $filtersResource->addParameterFilter($parameterFilter);

            }

            $filterOption = new FilterOption();
            $filterOption
                ->setId($parameterOption->getId())
                ->setName($parameterOption->getName())
            ;

            $parameterFilter->addOption($filterOption);

        }

        /**
         * @var \Shop\CatalogBundle\Entity\CategoryParameter $categoryParameter
         */
        foreach($category->getParameters() as $categoryParameter){

            $parameterFilter = $filtersResource->getParameterFilter($categoryParameter->getParameterId());
            if($parameterFilter){

                $parameterFilter->setType($categoryParameter->getParameter()->getType());

                $parameterFilter
                    ->setName($categoryParameter->getName())
                    ->setGroups($categoryParameter->getFilterGroups())
                ;

                if($categoryParameter->getParameter()->getDefaultOptionId()){

                    if(
                        !isset($parametersFilteredOptionIds[$categoryParameter->getParameterId()])
                        ||
                        (
                            isset($parametersFilteredOptionIds[$categoryParameter->getParameterId()])
                            && !$parametersFilteredOptionIds[$categoryParameter->getParameterId()]
                        )
                    ){
                        $parametersFilteredOptionIds[$categoryParameter->getParameterId()] = array(
                            $categoryParameter->getParameter()->getDefaultOptionId(),
                        );
                    }

                }

            }

        }

        foreach($parametersFilteredOptionIds as $parameterId => $filteredOptionIds) {

            $parameterFilter = $filtersResource->getParameterFilter($parameterId);

            if ($parameterFilter && $filteredOptionIds) {

                if (!is_array($filteredOptionIds)) {
                    $filteredOptionIds = array((int)$filteredOptionIds);
                }

                foreach($filteredOptionIds as $key => $filteredOptionId){

                    $parameterFilterOption = $parameterFilter->getOption($filteredOptionId);
                    if($parameterFilterOption){

                        $parameterFilterOption->setIsSelected();

                    } else {

                        unset($filteredOptionIds[$key]);

                    }

                }

                $parameterFilter->setFilteredOptionIds($filteredOptionIds);

            }

        }
    }

    /**
     * @param Request $request
     * @param Category $category
     * @return array
     */
    public function getPriceRageSteps(Request $request, Category $category)
    {
        $priceRangeSteps = $request->get('prices', json_decode($request->cookies->get('prices' . $category->getId()), true));
        if (!is_array($priceRangeSteps)) {
            $priceRangeSteps = array();
        }

        return $priceRangeSteps;

    }

    /**
     * @param Category $category
     * @param array $priceRangeSteps
     * @param FiltersResource $filtersResource
     * @return Filter
     */
    protected function buildPriceRangerFilter(Category $category, $priceRangeSteps = array(), FiltersResource $filtersResource){

        if(!is_array($priceRangeSteps)){
            $priceRangeSteps = array();
        }

        $filter = new Filter();
        $filter
            ->setType(FilterInterface::TYPE_CHECKBOXES)
            ->setGroups(array(
                FilterInterface::GROUP_EXTRA,
            ))
        ;
        $filteredOptionIds = array();

        $priceIntervalsData = $this->getProposalRepository()->getPriceIntervalsData($category->getId(), null, $filtersResource);
        foreach($priceIntervalsData['intervals'] as $step => $priceRange){

            $filterOption = new PriceRangeFilterOption();
            $filterOption
                ->setId($step)
                ->setMin($priceRange['min'])
                ->setMax($priceRange['max'])
                ->setName($this->moneyExtension->formatMoney($priceRange['min'], null, false) . ' - ' . $this->moneyExtension->formatMoney($priceRange['max']))
                ->setPricesAmount($priceRange['pricesAmount'])
                ->setIsSelected(in_array($step, $priceRangeSteps))
            ;

            if($filterOption->getIsSelected()){
                $filteredOptionIds[] = $filterOption->getId();
            }

            $filter->addOption($filterOption);

        }

        $filter->setFilteredOptionIds($filteredOptionIds);

        return $filter;

    }

} 