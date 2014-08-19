<?php
namespace Shop\CatalogBundle\Filter\SliderFilter;

use Shop\CatalogBundle\Filter\Filter;

/**
 * Class SliderFilter
 * @package Shop\CatalogBundle\Filter\SliderFilter
 */
class SliderFilter extends Filter implements SliderFilterInterface {

    /**
     * @var int
     */
    public $step;

    function __construct()
    {
        $this->type = self::TYPE_SLIDER;
    }

    /**
     * @return int
     */
    public function getStep()
    {
        return $this->step;
    }

    /**
     * @param int $step
     * @return $this
     */
    public function setStep($step)
    {
        $this->step = floatval($step);
        return $this;
    }

    /**
     * @param float $maxValue
     * @return $this
     */
    public function setMaxValue($maxValue)
    {
        return parent::setMaxValue(floatval($maxValue));
    }

    /**
     * @param float $minValue
     * @return $this
     */
    public function setMinValue($minValue)
    {
        return parent::setMinValue(floatval($minValue));
    }

}