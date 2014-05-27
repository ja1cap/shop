<?php
namespace Shop\CatalogBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class CategoryFiltersType
 * @package Shop\CatalogBundle\Form\Type
 */
class CategoryFiltersType extends AbstractType {

    /**
     * @var \Shop\CatalogBundle\Filter\FiltersBuilder
     */
    protected $filtersBuilder;

    function __construct($filtersBuilder)
    {
        $this->filtersBuilder = $filtersBuilder;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {

        $resolver->setRequired(array('category'));

        $resolver->setAllowedTypes(array(
            'category' => array('Shop\CatalogBundle\Entity\Category'),
        ));

    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        /**
         * @var $category \Shop\CatalogBundle\Entity\Category
         */
        $category = $options['category'];
        $filtersResource = $this->filtersBuilder->build($category);

        $builder
            ->add('name', 'text', array(
                'label' => 'Название',
            ))
            ->add('slug', 'text', array(
                'label' => 'Уникальный адрес (http://домен/catalog/:адрес)',
            ))
            ->add('manufacturerFilter', 'choice', array(
                'choice_list' => $filtersResource->getManufacturerFilter()->getChoiceList(),
                'multiple' => true,
                'required' => false,
                'label' => 'Производитель',
            ))
        ;

        foreach($filtersResource->getParameterFilters() as $parameterFilter){

            $builder
                ->add('parameterFilter' . $parameterFilter->getParameterId(), 'choice', array(
                    'property_path' => 'parameterFilters[' . $parameterFilter->getParameterId() . ']',
                    'choice_list' => $parameterFilter->getChoiceList(),
                    'multiple' => true,
                    'required' => false,
                    'label' => $parameterFilter->getName(),
                ))
            ;

        }

        $builder
            ->add('priceRangeFilter', 'choice', array(
                'choice_list' => $filtersResource->getPriceRangeFilter()->getChoiceList(),
                'multiple' => true,
                'required' => false,
                'label' => 'Цены',
            ))
        ;

        $builder
            ->add('save', 'submit', array(
                'label' => 'Сохранить',
            ));

    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'shop_catalog_category_filters_type';
    }

} 