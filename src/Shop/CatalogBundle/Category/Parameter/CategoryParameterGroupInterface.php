<?php
namespace Shop\CatalogBundle\Category\Parameter;

/**
 * Interface CategoryParameterGroupInterface
 * @package Shop\CatalogBundle\Category\Parameter
 */
interface CategoryParameterGroupInterface {

    /**
     * @return int
     */
    public function getId();

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Get parameters
     *
     * @return \Doctrine\Common\Collections\Collection|\Shop\CatalogBundle\Category\Parameter\CategoryParameterInterface[]
     */
    public function getParameters();

} 