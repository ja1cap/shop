<?php
namespace Shop\UserBundle\Mapper;

use Doctrine\Common\Persistence\ObjectRepository;
use Shop\CatalogBundle\Entity\Category;
use Shop\CatalogBundle\Entity\Contractor;
use Shop\UserBundle\Entity\ManagerContractor;

/**
 * Class ManagerContractorMapper
 * @package Shop\UserBundle\Mapper
 */
class ManagerContractorMapper {

    /**
     * @var ManagerContractor
     */
    protected $managerContractor;

    /**
     * @var ObjectRepository
     */
    protected $contractorRepository;

    /**
     * @var ObjectRepository
     */
    protected $categoryRepository;

    /**
     * @param ManagerContractor $managerContractor
     * @param ObjectRepository $categoryRepository
     * @param ObjectRepository $contractorRepository
     */
    function __construct(ManagerContractor $managerContractor, ObjectRepository $categoryRepository, ObjectRepository $contractorRepository)
    {
        $this->managerContractor = $managerContractor;
        $this->categoryRepository = $categoryRepository;
        $this->contractorRepository = $contractorRepository;
    }

    /**
     * @param $contractorId
     * @return $this
     */
    public function setContractorId($contractorId){

        if($contractorId == '*'){

            $this->managerContractor
                ->setAllContractors(true)
                ->setContractor(null)
            ;

        } else {

            $contractor = $this->contractorRepository->findOneBy(array(
                'id' => $contractorId,
            ));

            if(!$contractor instanceof Contractor){
                $contractor = null;
            }

            $this->managerContractor
                ->setAllContractors(false)
                ->setContractor($contractor)
            ;

        }

        return $this;

    }

    /**
     * @return int|string
     */
    public function getContractorId(){

        if($this->managerContractor->getAllContractors()){
            return '*';
        }

        return $this->managerContractor->getContractorId();

    }

    /**
     * @param $categoriesIds
     * @return $this
     */
    public function setCategoriesIds($categoriesIds){

        if(in_array('*', $categoriesIds)){

            $this->managerContractor
                ->setAllCategories(true)
                ->setCategories(null);
            ;

        } else {

            $categories = $this->categoryRepository->findBy(array(
                'id' => $categoriesIds
            ));

            $this->managerContractor
                ->setAllCategories(false)
                ->setCategories($categories)
            ;

        }

        return $this;

    }

    /**
     * @return array|string
     */
    public function getCategoriesIds(){

        if($this->managerContractor->getAllCategories()){
            return array('*');
        }

        return $this->managerContractor->getCategories()->map(function(Category $category){
            return $category->getId();
        })->toArray();

    }

} 