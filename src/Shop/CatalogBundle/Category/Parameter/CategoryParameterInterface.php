<?php
namespace Shop\CatalogBundle\Category\Parameter;

/**
 * Interface CategoryParameterInterface
 * @package Shop\CatalogBundle\Category\Parameter
 */
interface CategoryParameterInterface {

    /**
     * @return string
     */
    public function getName();

    /**
     * @return int
     */
    public function getParameterId();

    /**
     * @return boolean
     */
    public function getIsComparable();

} 