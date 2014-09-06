<?php
namespace Shop\CatalogBundle\Filter;

use Shop\CatalogBundle\Filter\OptionsFilter\OptionsFilterInterface;
use Shop\CatalogBundle\Filter\ParameterFilter\ParameterFilter;
use Shop\CatalogBundle\Filter\ParameterFilter\ParameterFilterInterface;
use Shop\CatalogBundle\Filter\Checkbox\CheckboxFilterInterface;

/**
 * Class FiltersResource
 * @package Shop\CatalogBundle\Filter
 */
class FiltersResource {

    /**
     * @var array
     */
    public static $filterGroups = array(
        FilterInterface::GROUP_NONE => 'Нет',
        FilterInterface::GROUP_MAIN => 'Основная',
        FilterInterface::GROUP_EXTRA => 'Дополнительная',
        FilterInterface::GROUP_PROPOSAL => 'Фильтры на странице товара',
    );

    /**
     * @var array
     */
    public static $filterTypes = array(
        FilterInterface::TYPE_SELECT => 'Выпадающий список',
        FilterInterface::TYPE_CHECKBOXES => 'Флажки',
//        FilterInterface::TYPE_IMAGE => 'Изображения',
//        FilterInterface::TYPE_IMAGE_WITH_TEXT => 'Изображения с текстом',
//        FilterInterface::TYPE_SLIDER => 'Слайдер',
    );

    /**
     * @var int|null
     */
    protected $categoryId;

    /**
     * @var int|null
     */
    protected $proposalId;

    /**
     * @var OptionsFilterInterface
     */
    protected $manufacturerFilter;

    /**
     * @var FilterInterface
     */
    protected $priceRangeFilter;

    /**
     * @var ParameterFilterInterface[]
     */
    protected $parameterFilters = array();

    /**
     * @var CheckboxFilterInterface
     */
    protected $isNewFilter;

    /**
     * @var CheckboxFilterInterface
     */
    protected $isBestsellerFilter;

    /**
     * @var CheckboxFilterInterface
     */
    protected $hasActionFilter;

    /**
     * @var CheckboxFilterInterface
     */
    protected $hasDiscountFilter;

    /**
     * @return int|null
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * @param int|null $categoryId
     * @return $this
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getProposalId()
    {
        return $this->proposalId;
    }

    /**
     * @param int|null $proposalId
     * @return $this
     */
    public function setProposalId($proposalId)
    {
        $this->proposalId = $proposalId;
        return $this;
    }

    /**
     * @param \Shop\CatalogBundle\Filter\OptionsFilter\OptionsFilterInterface[] $manufacturerFilter
     * @return $this
     */
    public function setManufacturerFilter($manufacturerFilter)
    {
        $this->manufacturerFilter = $manufacturerFilter;
        return $this;
    }

    /**
     * @return \Shop\CatalogBundle\Filter\OptionsFilter\OptionsFilterInterface
     */
    public function getManufacturerFilter()
    {
        return $this->manufacturerFilter;
    }

    /**
     * @param ParameterFilterInterface[] $parameterFilters
     * @return $this
     */
    public function setParameterFilters(array $parameterFilters = array())
    {
        $this->parameterFilters = $parameterFilters;
        return $this;
    }

    /**
     * @return ParameterFilterInterface[]
     */
    public function getParameterFilters()
    {
        return $this->parameterFilters;
    }

    /**
     * @param ParameterFilter $filter
     * @return $this
     */
    public function addParameterFilter(ParameterFilter $filter){
        $this->parameterFilters[$filter->getParameterId()] = $filter;
        return $this;
    }

    /**
     * @param $parameterId
     * @return null|ParameterFilter
     */
    public function getParameterFilter($parameterId){
        return isset($this->parameterFilters[$parameterId]) ? $this->parameterFilters[$parameterId] : null;
    }

    /**
     * @param $group
     * @return array
     */
    public function getGroupParameterFilters($group){

        $filters = array();

        foreach($this->parameterFilters as $parameterId => $parameterFilter){
            if(in_array($group, $parameterFilter->getGroups())){
                $filters[$parameterId] = $parameterFilter;
            }
        }

        return $filters;

    }

    /**
     * @return array
     */
    public function getMainParameterFilters(){
        return $this->getGroupParameterFilters(FilterInterface::GROUP_MAIN);
    }

    /**
     * @return array
     */
    public function getExtraParameterFilters(){
        return $this->getGroupParameterFilters(FilterInterface::GROUP_EXTRA);
    }

    /**
     * @return array
     */
    public function getProposalParameterFilters(){
        return $this->getGroupParameterFilters(FilterInterface::GROUP_PROPOSAL);
    }

    /**
     * @param \Shop\CatalogBundle\Filter\FilterInterface $priceRangeFilter
     * @return $this
     */
    public function setPriceRangeFilter($priceRangeFilter)
    {
        $this->priceRangeFilter = $priceRangeFilter;
        return $this;
    }

    /**
     * @return \Shop\CatalogBundle\Filter\FilterInterface
     */
    public function getPriceRangeFilter()
    {
        return $this->priceRangeFilter;
    }

    /**
     * @return CheckboxFilterInterface
     */
    public function getIsNewFilter()
    {
        return $this->isNewFilter;
    }

    /**
     * @param CheckboxFilterInterface $isNewFilter
     * @return $this
     */
    public function setIsNewFilter($isNewFilter)
    {
        $this->isNewFilter = $isNewFilter;
        return $this;
    }

    /**
     * @return CheckboxFilterInterface
     */
    public function getIsBestsellerFilter()
    {
        return $this->isBestsellerFilter;
    }

    /**
     * @param CheckboxFilterInterface $isBestsellerFilter
     * @return $this
     */
    public function setIsBestsellerFilter($isBestsellerFilter)
    {
        $this->isBestsellerFilter = $isBestsellerFilter;
        return $this;
    }

    /**
     * @return CheckboxFilterInterface
     */
    public function getHasActionFilter()
    {
        return $this->hasActionFilter;
    }

    /**
     * @param CheckboxFilterInterface $hasActionFilter
     */
    public function setHasActionFilter($hasActionFilter)
    {
        $this->hasActionFilter = $hasActionFilter;
    }

    /**
     * @return CheckboxFilterInterface
     */
    public function getHasDiscountFilter()
    {
        return $this->hasDiscountFilter;
    }

    /**
     * @param CheckboxFilterInterface $hasDiscountFilter
     */
    public function setHasDiscountFilter($hasDiscountFilter)
    {
        $this->hasDiscountFilter = $hasDiscountFilter;
    }

}