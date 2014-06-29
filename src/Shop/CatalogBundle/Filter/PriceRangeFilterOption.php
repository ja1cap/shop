<?php
namespace Shop\CatalogBundle\Filter;

/**
 * Class PriceRangeFilterOption
 * @package Shop\CatalogBundle\Filter
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