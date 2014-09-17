<?php
namespace Shop\CatalogBundle\Twig;

use Doctrine\Common\Persistence\ObjectRepository;
use Weasty\Bundle\CatalogBundle\Category\CategoryInterface;

/**
 * Class CategoryExtension
 * @package Shop\CatalogBundle\Twig
 */
class CategoryExtension extends \Twig_Extension {

    /**
     * @TODO refactor
     * @var \Weasty\Bundle\CatalogBundle\Category\CategoryInterface[]
     */
    private $categories;

    /**
     * @var ObjectRepository
     */
    private $categoryRepository;

    function __construct(ObjectRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {

        return array(
            new \Twig_SimpleFunction('shop_catalog_categories', array($this, 'getCategories'))
        );

    }

    /**
     * @return \Weasty\Bundle\CatalogBundle\Category\CategoryInterface[]
     */
    public  function getCategories(){

        if($this->categories === null){

            $this->categories = $this->categoryRepository->findBy(
                array(
                    'status' => CategoryInterface::STATUS_ON
                ),
                array(
                    'name' => 'ASC',
                )
            );

        }

        return $this->categories;

    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'shop_catalog_category';
    }

} 