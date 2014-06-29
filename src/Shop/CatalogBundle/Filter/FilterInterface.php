<?php
namespace Shop\CatalogBundle\Filter;

/**
 * Interface FilterInterface
 * @package Shop\CatalogBundle\Filter
 */
interface FilterInterface {

    const GROUP_NONE = 0;
    const GROUP_MAIN = 1;
    const GROUP_EXTRA = 2;
    const GROUP_PROPOSAL = 3;

    const TYPE_SELECT = 1;
    const TYPE_CHECKBOXES = 2;
    const TYPE_IMAGE = 3;
    const TYPE_IMAGE_WITH_TEXT = 4;

    /**
     * @return int
     */
    public function getType();

    /**
     * @return array
     */
    public function getGroups();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return int[]
     */
    public function getFilteredOptionIds();

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
     * @return \Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;
     */
    public function getChoiceList();

} 