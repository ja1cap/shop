<?php
namespace Shop\CatalogBundle\Filter\PriceRangeFilter;

use Shop\CatalogBundle\Filter\FilterInterface;
use Shop\CatalogBundle\Filter\FiltersResource;
use Shop\CatalogBundle\Filter\OptionsFilter\OptionsFilter;
use Shop\CatalogBundle\Filter\SliderFilter\SliderFilter;
use Weasty\Bundle\CatalogBundle\Category\CategoryInterface;

/**
 * Class PriceRangeFilterBuilder
 * @package Shop\CatalogBundle\Filter\PriceRange
 */
class PriceRangeFilterBuilder {

    /**
     * @var \Weasty\Money\Twig\AbstractMoneyExtension
     */
    protected $moneyExtension;

    /**
     * @var \Shop\CatalogBundle\Entity\ProposalRepository
     */
    protected $proposalRepository;

    /**
     * @param $proposalRepository
     * @param $moneyExtension
     */
    function __construct($proposalRepository, $moneyExtension)
    {
        $this->proposalRepository = $proposalRepository;
        $this->moneyExtension = $moneyExtension;
    }

    /**
     * @param \Weasty\Bundle\CatalogBundle\Category\CategoryInterface $category
     * @param array $value
     * @param FiltersResource $filtersResource
     * @param int $filterType
     * @return FilterInterface
     */
    public function build(CategoryInterface $category, $value = array(), FiltersResource $filtersResource = null, $filterType = null){

        switch($filterType){
            case FilterInterface::TYPE_SELECT:
            case FilterInterface::TYPE_CHECKBOXES:

                $filter = $this->buildOptionsFilter($category, $value, $filtersResource);
                break;

            case FilterInterface::TYPE_SLIDER:
            default:

                $filter = $this->buildSliderFilter($category, $value);


        }

        $filter
            ->setName('Цена')
            ->setGroups(array(
                FilterInterface::GROUP_EXTRA,
            ))
        ;

        return $filter;

    }

    /**
     * @param \Weasty\Bundle\CatalogBundle\Category\CategoryInterface $category
     * @param $value
     * @param FiltersResource $filtersResource = null
     * @return OptionsFilter
     */
    protected function buildOptionsFilter(CategoryInterface $category, $value, FiltersResource $filtersResource = null){

        $filter = new OptionsFilter();

        $selectedOptionIds = $value;
        if(!is_array($selectedOptionIds)){
            $selectedOptionIds = array();
        }

        $value = array();

        $priceIntervalsData = $this->proposalRepository->getPriceIntervalsData($category->getId(), null, $filtersResource);
        foreach($priceIntervalsData['intervals'] as $step => $priceRange){

            $filterOption = new PriceRangeFilterOption();
            $filterOption
                ->setId($step)
                ->setMin($priceRange['min'])
                ->setMax($priceRange['max'])
                ->setName($this->moneyExtension->formatMoney($priceRange['min'], null, false) . ' - ' . $this->moneyExtension->formatMoney($priceRange['max']))
                ->setPricesAmount($priceRange['pricesAmount'])
                ->setIsSelected(in_array($step, $selectedOptionIds))
            ;

            if($filterOption->getIsSelected()){
                $value[] = $filterOption->getId();
            }

            $filter->addOption($filterOption);

        }

        $filter->setValue($value);

        return $filter;

    }

    /**
     * @param \Weasty\Bundle\CatalogBundle\Category\CategoryInterface $category
     * @param mixed $value
     * @return SliderFilter
     */
    protected function buildSliderFilter(CategoryInterface $category, $value){

        $filter = new SliderFilter();
        $priceRange = $this->proposalRepository->getPriceRange($category->getId());

        $filter
            ->setStep(10000)
            ->setMinValue($priceRange['minPrice'])
            ->setMaxValue($priceRange['maxPrice'])
        ;

        if(isset($value['min']) && $value['min'] && $value['min'] != $filter->getMinValue()){
            $min = floatval(preg_replace("/([^0-9\\.])/i", "", $value['min']));
        } else {
            $min = $filter->getMinValue();
        }

        if(isset($value['max']) && $value['max'] && $value['max'] != $filter->getMaxValue()){
            $max = floatval(preg_replace("/([^0-9\\.])/i", "", $value['max']));
        } else {
            $max = $filter->getMaxValue();
        }

        $filterValue = array(
            'min' => $min,
            'max' => $max
        );

        $filter->setValue($filterValue);

        return $filter;

    }

} 