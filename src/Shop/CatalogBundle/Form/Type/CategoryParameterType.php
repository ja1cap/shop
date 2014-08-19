<?php
namespace Shop\CatalogBundle\Form\Type;

use Shop\CatalogBundle\Entity\Category;
use Shop\CatalogBundle\Filter\FiltersResource;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class CategoryParameterType
 * @package Shop\CatalogBundle\Form\Type
 */
class CategoryParameterType extends AbstractType {

    /**
     * @var Category
     */
    protected $category;

    /**
     * @var array
     */
    protected $availableParameters = array();

    /**
     * @param Category $category
     * @param array $availableParameters
     */
    function __construct(Category $category, array $availableParameters = array())
    {
        $this->category = $category;
        $this->availableParameters = $availableParameters;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('parameter', 'entity', array(
                'class' => 'ShopCatalogBundle:Parameter',
                'choices' => $this->getAvailableParameters(),
                'label' => 'Базовый параметр',
            ))
            ->add('filterGroups', 'choice', array(
                'choices' => FiltersResource::$filterGroups,
                'required' => false,
                'multiple' => true,
                'label' => 'Группа фильтров',
            ))
            ->add('name', 'text', array(
                'required' => false,
                'label' => 'Название параметра в категории',
                'attr' => array(
                    'placeholder' => 'Поумолчанию название базового параметра',
                ),
            ))
        ;

        if(!$this->getCategory()->getParameterGroups()->isEmpty()){
            $builder
                ->add('group', 'entity', [
                    'required' => false,
                    'label' => 'Группа параметров',
                    'empty_value' => 'Нет',
                    'class' => 'ShopCatalogBundle:CategoryParameterGroup',
                    'choices' => $this->getCategory()->getParameterGroups(),
                ])
            ;
        }

        $builder
            ->add('save', 'submit', array(
                'label' => 'Сохранить',
            ))
        ;

    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'shop_catalog_category_parameter';
    }

    /**
     * @return array
     */
    public function getAvailableParameters()
    {
        return $this->availableParameters;
    }

    /**
     * @return \Shop\CatalogBundle\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }

} 