<?php
namespace Shop\CatalogBundle\Filter\OptionsFilter;

use Shop\CatalogBundle\Filter\AbstractFilter;
use Symfony\Component\Form\Extension\Core\ChoiceList\SimpleChoiceList;

/**
 * Class OptionsFilter
 * @package Shop\CatalogBundle\Filter\OptionsFilter
 */
class OptionsFilter extends AbstractFilter implements OptionsFilterInterface {

    /**
     * @var FilterOptionInterface[]
     */
    public $options = array();

    function __construct()
    {
        $this->type = self::TYPE_SELECT;
    }

    /**
     * @return FilterOptionInterface[]
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param \Shop\CatalogBundle\Filter\OptionsFilter\FilterOptionInterface[] $options
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
     * @return null|FilterOptionInterface
     */
    public function getMinOption()
    {
        $option = current($this->options);
        reset($this->options);
        return $option;
    }

    /**
     * @return null|FilterOptionInterface
     */
    public function getMaxOption()
    {
        $option = end($this->options);
        reset($this->options);
        return $option;
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

    /**
     * @param $value
     * @return $this
     */
    public function setValue($value){

        $filteredOptionIds = array();

        if(is_array($value)) {

            foreach($value as $option){

                if($option instanceof FilterOptionInterface){
                    $filteredOptionIds[] = $option->getId();
                } else {
                    $filteredOptionIds[] = (int)$option;
                }

            }

        } elseif($value instanceof FilterOptionInterface){

            $filteredOptionIds[] = $value->getId();

        } else {

            $option = $this->getOption($value);
            if($option){
                $filteredOptionIds[] = $option->getId();
            }

        }

        $this->value = $filteredOptionIds;

        return $this;

    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return mixed
     */
    public function getMinValue()
    {
        $option = $this->getMinOption();
        return $option ? $option->getId() : null;
    }

    /**
     * @return mixed
     */
    public function getMaxValue()
    {
        $option = $this->getMaxOption();
        return $option ? $option->getId() : null;
    }

}