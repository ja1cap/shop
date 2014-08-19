<?php
namespace Shop\CatalogBundle\Filter\SliderFilter;

/**
 * Interface SliderFilterInterface
 * @package Shop\CatalogBundle\Filter\SliderFilter
 */
interface SliderFilterInterface {

    /**
     * @param $step
     * @return $this
     */
    public function setStep($step);

    /**
     * @return int
     */
    public function getStep();

} 