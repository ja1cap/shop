<?php
namespace Shop\CatalogBundle\Filter\OptionsFilter;

/**
 * Interface FilterOptionInterface
 * @package Shop\CatalogBundle\Filter\OptionsFilter
 */
interface FilterOptionInterface extends \JsonSerializable {

    /**
     * @return int
     */
    public function getId();

    /**
     * @return mixed
     */
    public function getValue();

    /**
     * @return string
     */
    public function getName();

    /**
     * @param boolean $flag
     * @return $this
     */
    public function setIsSelected($flag = true);

    /**
     * @return boolean
     */
    public function getIsSelected();

    /**
     * @param int $amount
     * @return $this
     */
    public function setPricesAmount($amount);

    /**
     * @return int
     */
    public function getPricesAmount();

    /**
     * @return string
     */
    public function __toString();

    /**
     * @return array
     */
    public function toArray();

}