<?php
namespace Shop\CatalogBundle\Filter;

/**
 * Class FilterOption
 * @package Shop\CatalogBundle\Filter
 */
class FilterOption implements FilterOptionInterface {

    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var boolean
     */
    public $isSelected = false;

    /**
     * @var int
     */
    public $pricesAmount = 0;

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param boolean $flag
     * @return $this
     */
    public function setIsSelected($flag = true)
    {
        $this->isSelected = $flag;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getIsSelected()
    {
        return $this->isSelected;
    }

    /**
     * @param int $amount
     * @return $this
     */
    public function setPricesAmount($amount)
    {
        $this->pricesAmount = $amount;
        return $this;
    }

    /**
     * @return int
     */
    public function getPricesAmount()
    {
        return $this->pricesAmount;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

} 