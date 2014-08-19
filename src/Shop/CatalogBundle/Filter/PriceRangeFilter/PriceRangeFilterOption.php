<?php
namespace Shop\CatalogBundle\Filter\PriceRangeFilter;

use Shop\CatalogBundle\Filter\OptionsFilter\FilterOption;

/**
 * Class PriceRangeFilterOption
 * @package Shop\CatalogBundle\Filter\PriceRangeFilter
 */
class PriceRangeFilterOption extends FilterOption {

    /**
     * @var float
     */
    public $min;

    /**
     * @var float
     */
    public $max;

    /**
     * @return int|mixed
     */
    public function getValue()
    {
        return $this->id;
    }

    /**
     * @param float $max
     * @return $this
     */
    public function setMax($max)
    {
        $this->max = $max;
        return $this;
    }

    /**
     * @return float
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * @param float $min
     * @return $this
     */
    public function setMin($min)
    {
        $this->min = $min;
        return $this;
    }

    /**
     * @return float
     */
    public function getMin()
    {
        return $this->min;
    }

} 