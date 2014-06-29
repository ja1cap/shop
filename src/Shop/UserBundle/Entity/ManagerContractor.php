<?php

namespace Shop\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Shop\CatalogBundle\Entity\Category;
use Shop\CatalogBundle\Entity\Contractor;
use Weasty\Doctrine\Entity\AbstractEntity;

/**
 * Class ManagerContractor
 * @package Shop\UserBundle\Entity
 */
class ManagerContractor extends AbstractEntity
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $contractorId;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set contractorId
     *
     * @param integer $contractorId
     * @return ManagerContractor
     */
    public function setContractorId($contractorId)
    {
        $this->contractorId = $contractorId;

        return $this;
    }

    /**
     * Get contractorId
     *
     * @return integer 
     */
    public function getContractorId()
    {
        return $this->contractorId;
    }

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $categories;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }

    /**
     * Add categories
     *
     * @param \Shop\CatalogBundle\Entity\Category $categories
     * @return ManagerContractor
     */
    public function addCategory(Category $categories)
    {
        $this->categories[] = $categories;

        return $this;
    }

    /**
     * Remove categories
     *
     * @param \Shop\CatalogBundle\Entity\Category $categories
     */
    public function removeCategory(Category $categories)
    {
        $this->categories->removeElement($categories);
    }

    /**
     * @param $categories
     * @return $this
     */
    public function setCategories($categories = array()){

        if(is_array($categories)){

            $this->categories = new ArrayCollection($categories);

        } elseif($categories instanceof ArrayCollection){

            $this->categories = $categories;

        } else {

            $this->categories = new ArrayCollection();

        }

        return $this;

    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @return array
     */
    public function getCategoryIds(){
        return $this->getCategories()->map(function(Category $category){
            return $category->getId();
        })->toArray();
    }

    /**
     * @return array
     */
    public function getCategoryNames(){
        return $this->getCategories()->map(function(Category $category){
            return $category->getName();
        })->toArray();
    }

    /**
     * @var integer
     */
    private $managerId;

    /**
     * @var \Shop\UserBundle\Entity\Manager
     */
    private $manager;

    /**
     * @var \Shop\CatalogBundle\Entity\Contractor
     */
    private $contractor;


    /**
     * Set managerId
     *
     * @param integer $managerId
     * @return ManagerContractor
     */
    public function setManagerId($managerId)
    {
        $this->managerId = $managerId;

        return $this;
    }

    /**
     * Get managerId
     *
     * @return integer 
     */
    public function getManagerId()
    {
        return $this->managerId;
    }

    /**
     * Set manager
     *
     * @param \Shop\UserBundle\Entity\Manager $manager
     * @return ManagerContractor
     */
    public function setManager(Manager $manager = null)
    {
        $this->manager = $manager;
        $this->managerId = $manager ? $manager->getId() : null;
        return $this;
    }

    /**
     * Get manager
     *
     * @return \Shop\UserBundle\Entity\Manager 
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * Set contractor
     *
     * @param \Shop\CatalogBundle\Entity\Contractor $contractor
     * @return ManagerContractor
     */
    public function setContractor(Contractor $contractor = null)
    {
        $this->contractor = $contractor;
        $this->contractorId = $contractor ? $contractor->getId() : null;
        return $this;
    }

    /**
     * Get contractor
     *
     * @return \Shop\CatalogBundle\Entity\Contractor 
     */
    public function getContractor()
    {
        return $this->contractor;
    }
    /**
     * @var boolean
     */
    private $allCategories;


    /**
     * Set allCategories
     *
     * @param boolean $allCategories
     * @return ManagerContractor
     */
    public function setAllCategories($allCategories)
    {
        $this->allCategories = $allCategories;

        return $this;
    }

    /**
     * Get allCategories
     *
     * @return boolean 
     */
    public function getAllCategories()
    {
        return $this->allCategories;
    }
    /**
     * @var boolean
     */
    private $allContractors;


    /**
     * Set allContractors
     *
     * @param boolean $allContractors
     * @return ManagerContractor
     */
    public function setAllContractors($allContractors)
    {
        $this->allContractors = $allContractors;

        return $this;
    }

    /**
     * Get allContractors
     *
     * @return boolean 
     */
    public function getAllContractors()
    {
        return $this->allContractors;
    }
}
