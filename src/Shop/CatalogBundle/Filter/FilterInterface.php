<?php
namespace Shop\CatalogBundle\Filter;

/**
 * Interface FilterInterface
 * @package Shop\CatalogBundle\Filter
 */
interface FilterInterface extends \JsonSerializable {

    const GROUP_NONE = 0;
    const GROUP_MAIN = 1;
    const GROUP_EXTRA = 2;
    const GROUP_PROPOSAL = 3;

    const TYPE_SELECT = 1;
    const TYPE_CHECKBOXES = 2;
    const TYPE_IMAGE = 3;
    const TYPE_IMAGE_WITH_TEXT = 4;
    const TYPE_SLIDER = 5;
    const TYPE_CHECKBOX = 6;

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
     * @return mixed
     */
    public function getValue();

    /**
     * @param $value
     * @return $this
     */
    public function setValue($value);

    /**
     * @return mixed
     */
    public function getMinValue();

    /**
     * @return mixed
     */
    public function getMaxValue();

    /**
     * @return array
     */
    public function toArray();

}