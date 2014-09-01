<?php
namespace Weasty\Bundle\CatalogBundle\Form\Type;

use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Weasty\Bundle\CatalogBundle\Category\CategoryInterface;

/**
 * Class CategoryIdType
 * @package Weasty\Bundle\CatalogBundle\Form\Type
 */
class CategoryIdType extends AbstractType {

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
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {

        $categories = $this->getCategoryRepository()->findBy(array(), array(
            'name' => 'ASC',
        ));

        $choices = array();

        foreach($categories as $category){
            if($category instanceof CategoryInterface){
                $choices[$category->getId()] = $category->getName();
            }
        }

        $resolver
            ->setDefaults(array(
                'choices' => $choices,
                'label' => 'Категория',
            ));

    }

    /**
     * @return ObjectRepository
     */
    public function getCategoryRepository()
    {
        return $this->categoryRepository;
    }

    /**
     * @return null|string|\Symfony\Component\Form\FormTypeInterface
     */
    public function getParent()
    {
        return 'choice';
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'weasty_catalog_category_id';
    }

} 