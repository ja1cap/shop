<?php
namespace Shop\CatalogBundle\Filter\OptionsFilter;

use Shop\CatalogBundle\Filter\FilterInterface;

/**
 * Interface OptionsFilterInterface
 * @package Shop\CatalogBundle\Filter\OptionsFilter
 */
interface OptionsFilterInterface extends FilterInterface {

    /**
     * @return FilterOptionInterface[]
     */
    public function getOptions();

    /**
     * @param FilterOptionInterface $filterOption
     * @return $this
     */
    public function addOption(FilterOptionInterface $filterOption);

    /**
     * @param $id
     * @return null|FilterOptionInterface
     */
    public function getOption($id);

    /**
     * @return null|FilterOptionInterface
     */
    public function getMinOption();

    /**
     * @return null|FilterOptionInterface
     */
    public function getMaxOption();

    /**
     * @return \Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;
     */
    public function getChoiceList();

} 