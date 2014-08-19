<?php
namespace Shop\CatalogBundle\Filter\ParameterFilter;
use Shop\CatalogBundle\Filter\OptionsFilter\OptionsFilterInterface;

/**
 * Interface ParameterFilterInterface
 * @package Shop\CatalogBundle\Filter
 */
interface ParameterFilterInterface extends OptionsFilterInterface {

    /**
     * @return int
     */
    public function getParameterId();

    /**
     * @return \Shop\CatalogBundle\Entity\ParameterOption[]
     */
    public function getOptions();

} 