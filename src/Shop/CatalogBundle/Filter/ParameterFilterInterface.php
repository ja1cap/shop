<?php
namespace Shop\CatalogBundle\Filter;

/**
 * Interface ParameterFilterInterface
 * @package Shop\CatalogBundle\Filter
 */
interface ParameterFilterInterface extends FilterInterface {

    /**
     * @return int
     */
    public function getParameterId();

    /**
     * @return \Shop\CatalogBundle\Entity\ParameterOption[]
     */
    public function getOptions();

} 