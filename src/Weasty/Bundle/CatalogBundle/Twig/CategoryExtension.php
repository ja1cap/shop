<?php
namespace Weasty\Bundle\CatalogBundle\Twig;

use Doctrine\Common\Persistence\ObjectRepository;
use Weasty\Bundle\CatalogBundle\Data\CategoryInterface;

/**
 * Class CategoryExtension
 * @package Weasty\Bundle\CatalogBundle\Twig
 */
class CategoryExtension extends \Twig_Extension {

    /**
     * @var ObjectRepository
     */
    protected $categoryRepository;

    /**
     * @param ObjectRepository $categoryRepository
     */
    function __construct(ObjectRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @return ObjectRepository
     */
    public function getCategoryRepository()
    {
        return $this->categoryRepository;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('weasty_catalog_category_names', array($this, 'getCategoryNames')),
        );
    }

    /**
     * @return array
     */
    public function getFunctions(){
        return array(
            new \Twig_SimpleFunction('weasty_catalog_category', array($this, 'getCategory')),
            new \Twig_SimpleFunction('weasty_catalog_categories', array($this, 'getCategories')),
            new \Twig_SimpleFunction('weasty_catalog_category_names', array($this, 'getCategoryNames')),
        );
    }

    /**
     * @param $id
     * @return null|object
     */
    public function getCategory($id){
        return $id ? $this->getCategoryRepository()->findOneBy(array('id' => $id)) : null;
    }

    /**
     * @param $ids
     * @return array|null
     */
    public function getCategories($ids){
        return $ids ? $this->getCategoryRepository()->findBy(array('id' => $ids), array('name' => 'ASC')) : null;
    }

    /**
     * @param $categories
     * @return null|string
     */
    public function getCategoryNames($categories){

        if(is_numeric(current($categories))){
            $categories = $this->getCategories(array_filter($categories, function($id){
                return is_numeric($id);
            }));
        }

        if(!$categories){
            return null;
        }

        return implode(', ', array_filter(array_map(function($category){
            return $category instanceof CategoryInterface ? $category->getName() : null;
        }, $categories)));

    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'weasty_catalog_category';
    }

} 