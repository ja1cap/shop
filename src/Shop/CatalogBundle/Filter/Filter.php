<?php
namespace Shop\CatalogBundle\Filter;

/**
 * Class Filter
 * @package Shop\CatalogBundle\Filter
 */
class Filter extends AbstractFilter {

    /**
     * @var mixed
     */
    public $minValue;

    /**
     * @var mixed
     */
    public $maxValue;

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMaxValue()
    {
        return $this->maxValue;
    }

    /**
     * @param mixed $maxValue
     * @return $this
     */
    public function setMaxValue($maxValue)
    {
        $this->maxValue = $maxValue;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMinValue()
    {
        return $this->minValue;
    }

    /**
     * @param mixed $minValue
     * @return $this
     */
    public function setMinValue($minValue)
    {
        $this->minValue = $minValue;
        return $this;
    }

} 