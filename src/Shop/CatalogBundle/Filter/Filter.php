<?php
namespace Shop\CatalogBundle\Filter;
use Symfony\Component\Form\Extension\Core\ChoiceList\SimpleChoiceList;

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
     * @var array
     */
    public $groups = array();

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
     * @param array $groups
     * @return $this
     */
    public function setGroups($groups)
    {
        $this->groups = $groups;
        return $this;
    }

    /**
     * @return array
     */
    public function getGroups()
    {
        return $this->groups;
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

    /**
     * @return \Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;
     */
    public function getChoiceList()
    {
        $choices = array();
        foreach($this->options as $option){
            $choices[$option->getId()] = (string)$option;
        }
        return new SimpleChoiceList($choices);
    }

}