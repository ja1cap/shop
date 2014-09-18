<?php
namespace Shop\CatalogBundle\Estimator\Feature;

use Weasty\Bundle\CatalogBundle\Feature\FeatureInterface;

/**
 * Interface EstimatedFeatureInterface
 * @package Shop\CatalogBundle\Estimator\Feature
 */
interface EstimatedFeatureInterface extends FeatureInterface {

    const PRIORITY_ORDER_ASC = 1;
    const PRIORITY_ORDER_DESC = 2;

    /**
     * @return int
     */
    public function getPriorityOrder();

    /**
     * @param int $priorityOrder
     * @return $this
     */
    public function setPriorityOrder($priorityOrder);

    /**
     * @return int
     */
    public function getBestPriority();

    /**
     * @param int $maxPriority
     * @return $this
     */
    public function setBestPriority($maxPriority);

    /**
     * @return boolean
     */
    public function getHasEqualValues();

    /**
     * @param boolean $hasEqualValues
     * @return $this
     */
    public function setHasEqualValues($hasEqualValues);

    /**
     * @return boolean
     */
    public function getIsComparable();

    /**
     * @param boolean $isComparable
     * @return $this
     */
    public function setIsComparable($isComparable);

} 