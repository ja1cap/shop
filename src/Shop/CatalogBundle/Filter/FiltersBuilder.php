<?php
namespace Shop\CatalogBundle\Filter;

use Doctrine\Common\Collections\ArrayCollection;
use Shop\CatalogBundle\Entity\CategoryFilters;
use Shop\CatalogBundle\Filter\Checkbox\CheckboxFilter;
use Shop\CatalogBundle\Filter\ManufacturerFilter\ManufacturerFilterBuilder;
use Shop\CatalogBundle\Filter\OptionsFilter\FilterOption;
use Shop\CatalogBundle\Filter\OptionsFilter\OptionsFilterInterface;
use Shop\CatalogBundle\Filter\ParameterFilter\ParameterFilter;
use Shop\CatalogBundle\Filter\PriceRangeFilter\PriceRangeFilterBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;
use Weasty\Bundle\CatalogBundle\Category\CategoryInterface;
use Weasty\Bundle\CatalogBundle\Proposal\ProposalInterface;

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
     * @var \Weasty\Money\Twig\AbstractMoneyExtension
     */
    protected $moneyExtension;

    /**
     * @var \Shop\CatalogBundle\Entity\ProposalRepository
     */
    protected $proposalRepository;

    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    protected $categoryParameterRepository;

    /**
     * @var \Shop\CatalogBundle\Filter\ManufacturerFilter\ManufacturerFilterBuilder
     */
    private $manufacturerBuilder;

    /**
     * @var \Shop\CatalogBundle\Filter\PriceRangeFilter\PriceRangeFilterBuilder
     */
    private $priceRangeBuilder;

    function __construct($proposalRepository, $categoryParameterRepository, $moneyExtension, $cache)
    {
        $this->proposalRepository = $proposalRepository;
        $this->categoryParameterRepository = $categoryParameterRepository;
        $this->moneyExtension = $moneyExtension;
        $this->cache = $cache;
    }

    /**
     * @param CategoryInterface $category
     * @param \Weasty\Bundle\CatalogBundle\Proposal\ProposalInterface $proposal
     * @param array $manufacturerIds
     * @param array $parametersFilteredOptionIds
     * @param array $priceRangeSteps
     * @param array $extraFiltersData
     * @return FiltersResource
     */
    public function build(
        CategoryInterface $category = null,
        ProposalInterface $proposal = null,
        $manufacturerIds = array(),
        $parametersFilteredOptionIds = array(),
        $priceRangeSteps = array(),
        $extraFiltersData = array()
    ){

        $categoryId = ($category ? $category->getId() : null);
        $proposalId = ($proposal ? $proposal->getId() : null);

        $cacheId = 'category_filters_resource_' . md5(serialize(array(
            'categoryId' => $categoryId,
            'proposalId' => $proposalId,
            'manufacturer' => $manufacturerIds,
            self::PARAMETER_VALUES_FILTER_COOKIE_NAME => $parametersFilteredOptionIds,
            'prices' => $priceRangeSteps,
            'extra' => $extraFiltersData,
        )));

        $filtersResource = $this->cache->fetch($cacheId);
//        if(true){
        if(!$filtersResource){

            $filtersResource = new FiltersResource($cacheId);
            $filtersResource
                ->setCategoryId($categoryId)
                ->setProposalId($proposalId)
            ;

            $this->setExtraFilters($extraFiltersData, $filtersResource);

            if($category){

                $manufacturerFilter = $this->buildManufacturerFilter($category, $manufacturerIds);
                $filtersResource->setManufacturerFilter($manufacturerFilter);

                $this->buildParameterFilters($category, $parametersFilteredOptionIds, $filtersResource);

                $priceRangeFilter = $this->buildPriceRangeFilter($category, $priceRangeSteps, $filtersResource);
                $filtersResource->setPriceRangeFilter($priceRangeFilter);

                foreach($filtersResource->getParameterFilters() as $parameterFilter){

                    $parameterOptionsPricesAmount = $this->proposalRepository->getParameterOptionsPricesAmount($parameterFilter->getParameterId(), $category->getId(), ($proposal ? $proposal->getId() : null), $filtersResource);
                    foreach($parameterOptionsPricesAmount as $optionId => $pricesAmount){
                        $option = $parameterFilter->getOption($optionId);
                        if($option){
                            $option->setPricesAmount($pricesAmount);
                        }
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
     * @param CategoryInterface $category
     * @param \Weasty\Bundle\CatalogBundle\Proposal\ProposalInterface $proposal
     * @param Request $request
     * @return mixed|FiltersResource
     */
    public function buildFromRequest(CategoryInterface $category, ProposalInterface $proposal = null, Request $request){

        $manufacturer = null;
        $parametersValues = null;
        $priceRange = null;
        $extraFiltersData = [
            'isNew' => false,
            'isBestseller' => false,
            'hasAction' => false,
            'hasDiscount' => false,
        ];

        if(!$request->get('reset_filters')){

            $manufacturer = $request->get('manufacturer', json_decode($request->cookies->get('manufacturer'), true));
            $parametersValues = $this->getParametersFilterValues($request);
            $priceRange = $this->getPriceRage($request, $category);

        }

        foreach($extraFiltersData as $key => $value){
            $extraFiltersData[$key] = $request->get($key);
        }

        return $this->build($category, $proposal, $manufacturer, $parametersValues, $priceRange, $extraFiltersData);

    }

    /**
     * @param CategoryInterface $category
     * @param Request $request
     * @param Response $response
     * @return $this
     */
    public function setFiltersCookies(CategoryInterface $category, Request $request, Response $response){

        $cookiesData = [
            [
                'name' => self::PARAMETER_VALUES_FILTER_COOKIE_NAME,
                'parameter' => 'parameters',
            ],
            [
                'name' => 'manufacturer',
                'parameter' => 'manufacturer',
            ],
            [
                'name' => 'prices' . $category->getId(),
                'parameter' => 'prices',
            ],
        ];

        if($request->get('reset_filters')){

            foreach($cookiesData as $cookieData){

                $response->headers->clearCookie($cookieData['name']);

            }

        } else {

            foreach($cookiesData as $cookieData){

                if($request->query->has($cookieData['parameter'])){
                    $response->headers->setCookie(new Cookie($cookieData['name'], json_encode($request->query->get($cookieData['parameter']))));
                }

            }

        }

        return $this;

    }

    /**
     * @param array $extraFiltersData
     * @param FiltersResource $filtersResource
     * @return $this
     */
    public function setExtraFilters($extraFiltersData = array(), FiltersResource $filtersResource){

        if(!$extraFiltersData){
            return $this;
        }

        $extraFiltersCollection = new ArrayCollection($extraFiltersData);

        if($extraFiltersCollection->get('hasDiscount')){

            $hasDiscountFilter = new CheckboxFilter();
            $hasDiscountFilter
                ->setName('Скидка')
                ->setValue(true)
            ;
            $filtersResource->setHasDiscountFilter($hasDiscountFilter);

        }

        if($extraFiltersCollection->get('hasAction')){

            $hasActionFilter = new CheckboxFilter();
            $hasActionFilter
                ->setName('Акция')
                ->setValue(true)
            ;
            $filtersResource->setHasActionFilter($hasActionFilter);

        }

        if($extraFiltersCollection->get('isBestseller')){

            $isBestsellerFilter = new CheckboxFilter();
            $isBestsellerFilter
                ->setName('Хиты продаж')
                ->setValue(true)
            ;
            $filtersResource->setIsBestsellerFilter($isBestsellerFilter);

        }

        if($extraFiltersCollection->get('isNew')){

            $isNewFilter = new CheckboxFilter();
            $isNewFilter
                ->setName('Новинки')
                ->setValue(true)
            ;

            $filtersResource->setIsNewFilter($isNewFilter);

        }

        return $this;

    }

    /**
     * @param CategoryInterface $category
     * @param int[] $value
     * @return OptionsFilterInterface[]
     */
    public function buildManufacturerFilter(CategoryInterface $category, $value = array())
    {

        if(!$this->manufacturerBuilder){
            $this->manufacturerBuilder = new ManufacturerFilterBuilder($this->proposalRepository);
        }

        return $this->manufacturerBuilder->build($category, $value);

    }

    /**
     * @param Request $request
     * @return array
     */
    protected function getParametersFilterValues(Request $request)
    {
        $cookieName = self::PARAMETER_VALUES_FILTER_COOKIE_NAME;
        $cookie = $request->cookies->get($cookieName);

        if ($cookie) {

            $cookie = json_decode($request->cookies->get($cookieName), true);
            if (!is_array($cookie)) {
                $cookie = null;
            }

        }

        $values = $request->get('parameters', $cookie);

        if (is_array($values)) {
            $values = array_filter($values);
        } else {
            $values = array();
        }

        return $values;

    }

    /**
     * @param CategoryInterface $category
     * @param mixed $parametersValues
     * @param FiltersResource $filtersResource
     */
    public function buildParameterFilters(CategoryInterface $category, $parametersValues, FiltersResource $filtersResource)
    {

        $parametersOptions = $this->proposalRepository->findCategoryParametersOptions($category->getId());

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
         * @var \Shop\CatalogBundle\Entity\CategoryParameter[] $categoryParameters
         */
        $categoryParameters = $this->categoryParameterRepository->findBy(array(
            'categoryId' => $category->getId(),
        ));
        foreach($categoryParameters as $categoryParameter){

            $parameterFilter = $filtersResource->getParameterFilter($categoryParameter->getParameterId());
            if($parameterFilter){

                $parameterFilter->setType($categoryParameter->getParameter()->getType());

                $parameterFilter
                    ->setName($categoryParameter->getName())
                    ->setGroups($categoryParameter->getFilterGroups())
                ;

                if($categoryParameter->getParameter()->getDefaultOptionId()){

                    if(
                        !isset($parametersValues[$categoryParameter->getParameterId()])
                        ||
                        (
                            isset($parametersValues[$categoryParameter->getParameterId()])
                            && !$parametersValues[$categoryParameter->getParameterId()]
                        )
                    ){
                        $parametersValues[$categoryParameter->getParameterId()] = array(
                            $categoryParameter->getParameter()->getDefaultOptionId(),
                        );
                    }

                }

            }

        }

        foreach($parametersValues as $parameterId => $parameterFilterValue) {

            $parameterFilter = $filtersResource->getParameterFilter($parameterId);

            if ($parameterFilter && $parameterFilterValue) {

                if (!is_array($parameterFilterValue)) {
                    $parameterFilterValue = array((int)$parameterFilterValue);
                }

                foreach($parameterFilterValue as $key => $optionId){

                    $parameterFilterOption = $parameterFilter->getOption($optionId);
                    if($parameterFilterOption){

                        $parameterFilterOption->setIsSelected();

                    } else {

                        unset($parameterFilterValue[$key]);

                    }

                }

                $parameterFilter->setValue($parameterFilterValue);

            }

        }
    }

    /**
     * @param Request $request
     * @param CategoryInterface $category
     * @return array
     */
    public function getPriceRage(Request $request, CategoryInterface $category)
    {
        $priceRange = $request->get('prices', json_decode($request->cookies->get('prices' . $category->getId()), true));
        if (!is_array($priceRange)) {
            $priceRange = array();
        }

        return $priceRange;

    }

    /**
     * @param CategoryInterface $category
     * @param array $priceRange
     * @param FiltersResource $filtersResource
     * @return FilterInterface
     */
    protected function buildPriceRangeFilter(CategoryInterface $category, $priceRange = array(), FiltersResource $filtersResource){

        if(!$this->priceRangeBuilder){
            $this->priceRangeBuilder = new PriceRangeFilterBuilder($this->proposalRepository, $this->moneyExtension);
        }

        return $this->priceRangeBuilder->build($category, $priceRange, $filtersResource);

    }

} 