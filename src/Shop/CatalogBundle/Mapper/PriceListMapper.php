<?php
namespace Shop\CatalogBundle\Mapper;

use Shop\CatalogBundle\Entity\Category;
use Shop\CatalogBundle\Entity\Contractor;
use Shop\CatalogBundle\Entity\Manufacturer;

/**
 * Class PriceListMapper
 * @package Shop\CatalogBundle\Mapper
 */
class PriceListMapper {

    /**
     * @var string
     */
    protected $name;

    /**
     * @var Category
     */
    protected $category;

    /**
     * @var array
     */
    protected $manufacturers = array();

    /**
     * @var array
     */
    protected $contractors = array();

    /**
     * @param \Shop\CatalogBundle\Entity\Category $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return \Shop\CatalogBundle\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param array $contractors
     */
    public function setContractors($contractors)
    {
        $this->contractors = $contractors;
    }

    /**
     * @return array
     */
    public function getContractors()
    {
        return $this->contractors;
    }

    /**
     * @param array $manufacturers
     */
    public function setManufacturers($manufacturers)
    {
        $this->manufacturers = $manufacturers;
    }

    /**
     * @return array
     */
    public function getManufacturers()
    {
        return $this->manufacturers;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int|null
     */
    public function getCategoryId(){
        return $this->getCategory() ? $this->getCategory()->getId() : null;
    }

    /**
     * @return array
     */
    public function getManufacturersIds(){
        return array_map(function(Manufacturer $manufacturer){
            return $manufacturer->getId();
        }, $this->getManufacturers());
    }

    /**
     * @return array
     */
    public function getContractorsIds(){
        return array_map(function(Contractor $contractor){
            return $contractor->getId();
        }, $this->getContractors());
    }

}