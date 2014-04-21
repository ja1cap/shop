<?php
namespace Weasty\CatalogBundle\Twig;

use Doctrine\Common\Persistence\ObjectRepository;
use Weasty\CatalogBundle\Data\CategoryInterface;

/**
 * Class CategoryExtension
 * @package Weasty\CatalogBundle\Twig
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
     * @param $ids
     * @return null|string
     */
    public function getCategoryNames($ids){
        $categories = $this->getCategories($ids);
        if(!$categories){
            return null;
        }
        return implode(', ', array_map(function(CategoryInterface $category){
            return $category->getName();
        }, $categories));
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