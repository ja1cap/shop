<?php
namespace Shop\CatalogBundle\Filter\ManufacturerFilter;

use Shop\CatalogBundle\Filter\FilterInterface;
use Shop\CatalogBundle\Filter\OptionsFilter\FilterOption;
use Shop\CatalogBundle\Filter\OptionsFilter\OptionsFilter;
use Weasty\Bundle\CatalogBundle\Category\CategoryInterface;

/**
 * Class ManufacturerFilterBuilder
 * @package Shop\CatalogBundle\Filter\ManufacturerFilter
 */
class ManufacturerFilterBuilder {

    /**
     * @var \Shop\CatalogBundle\Entity\ProposalRepository
     */
    protected $proposalRepository;

    function __construct($proposalRepository)
    {
        $this->proposalRepository = $proposalRepository;
    }

    /**
     * @param \Weasty\Bundle\CatalogBundle\Category\CategoryInterface $category
     * @param array $value
     * @return OptionsFilter
     */
    public function build(CategoryInterface $category, $value = array()){

        if ($value && !is_array($value)) {
            $value = array(
                (int)$value
            );
        } elseif(!$value) {
            $value = array();
        }

        $filter = new OptionsFilter();
        $filter
            ->setName('Производитель')
            ->setGroups(array(
                FilterInterface::GROUP_MAIN,
            ))
        ;
        $filteredOptionIds = array();

        /**
         * @var \Shop\CatalogBundle\Entity\Manufacturer $manufacturer
         */
        $categoryManufacturers = $this->proposalRepository->findCategoryManufacturers($category->getId());

        foreach ($categoryManufacturers as $manufacturer) {

            $filterOption = new FilterOption();
            $filterOption
                ->setId($manufacturer->getId())
                ->setName($manufacturer->getName())
            ;

            if(in_array($filterOption->getId(), $value)){

                $filteredOptionIds[] = $filterOption->getId();
                $filterOption->setIsSelected();

            }

            $filter->addOption($filterOption);

        }

        $filter->setValue($filteredOptionIds);

        return $filter;


    }

} 