<?php
namespace Shop\CatalogBundle\Filter;

/**
 * Class Filter
 * @package Shop\CatalogBundle\Filter
 */
class Filter implements FilterInterface {

    /**
     * @var string
     */
    public $name;

    /**
     * @var int
     */
    protected $type = self::TYPE_SELECT;

    /**
     * @var int
     */
    public $group = self::GROUP_EXTRA;

    /**
     * @var int[]
     */
    public $filteredOptionIds = array();

    /**
     * @var FilterOptionInterface[]
     */
    public $options = array();

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param int $group
     * @return $this
     */
    public function setGroup($group)
    {
        $this->group = $group;
        return $this;
    }

    /**
     * @return int
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param int $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return int[]
     */
    public function getFilteredOptionIds()
    {
        return $this->filteredOptionIds;
    }

    /**
     * @return FilterOptionInterface[]
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param \int[] $filteredOptionIds
     * @return $this
     */
    public function setFilteredOptionIds($filteredOptionIds)
    {
        $this->filteredOptionIds = $filteredOptionIds;
        return $this;
    }

    /**
     * @param \Shop\CatalogBundle\Filter\FilterOptionInterface[] $options
     * @return $this
     */
    public function setOptions($options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @param FilterOptionInterface $filterOption
     * @return $this
     */
    public function addOption(FilterOptionInterface $filterOption){
        $this->options[$filterOption->getId()] = $filterOption;
        return $this;
    }

    /**
     * @param $id
     * @return null|FilterOptionInterface
     */
    public function getOption($id){
        return isset($this->options[$id]) ? $this->options[$id] : null;
    }

} 