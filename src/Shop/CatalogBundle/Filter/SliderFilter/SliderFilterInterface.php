<?php
namespace Shop\CatalogBundle\Filter\SliderFilter;

use Shop\CatalogBundle\Filter\FilterInterface;

/**
 * Interface SliderFilterInterface
 * @package Shop\CatalogBundle\Filter\SliderFilter
 */
interface SliderFilterInterface extends FilterInterface {

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