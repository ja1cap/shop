<?php
namespace Shop\CatalogBundle\Twig;

use Doctrine\Common\Persistence\ObjectRepository;
use Weasty\CatalogBundle\Data\CategoryInterface;

/**
 * Class ShopCategoryExtension
 * @package Shop\CatalogBundle\Twig
 */
class ShopCategoryExtension extends \Twig_Extension {

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
     * @return array
     */
    public  function getCategories(){
        return $this->categoryRepository->findBy(
            array(
                'status' => CategoryInterface::STATUS_ON
            ),
            array(
                'name' => 'ASC',
            )
        );
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