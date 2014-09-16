<?php
namespace Shop\CatalogBundle\Category\Parameter;

/**
 * Interface CategoryParameterInterface
 * @package Shop\CatalogBundle\Category\Parameter
 */
interface CategoryParameterInterface {

    /**
     * @return int
     */
    public function getId();

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

    /**
     * Get groupId
     *
     * @return integer
     */
    public function getGroupId();

} 