<?php
namespace Shop\CatalogBundle\Filter;

/**
 * Class CategoryFiltersResource
 * @package Shop\CatalogBundle\Filter
 */
class CategoryFiltersResource {

    /**
     * @var array
     */
    public static $filterGroups = array(
        FilterInterface::GROUP_MAIN => 'Основная',
        FilterInterface::GROUP_EXTRA => 'Дополнительная',
        FilterInterface::GROUP_NONE => 'Нет',
    );

    /**
     * @var array
     */
    public static $filterTypes = array(
        FilterInterface::TYPE_SELECT => 'Выпадающий список',
        FilterInterface::TYPE_CHECKBOXES => 'Флажки',
//        FilterInterface::TYPE_IMAGE => 'Изображения',
//        FilterInterface::TYPE_IMAGE_WITH_TEXT => 'Изображения с текстом',
    );

    /**
     * @var FilterInterface
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
     * @param \Shop\CatalogBundle\Filter\FilterInterface[] $manufacturerFilter
     * @return $this
     */
    public function setManufacturerFilter($manufacturerFilter)
    {
        $this->manufacturerFilter = $manufacturerFilter;
        return $this;
    }

    /**
     * @return \Shop\CatalogBundle\Filter\FilterInterface
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
            if($parameterFilter->getGroup() == $group){
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

}