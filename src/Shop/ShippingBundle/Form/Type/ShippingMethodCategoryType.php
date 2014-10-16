<?php
namespace Shop\ShippingBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ShippingMethodCategoryType
 * @package Shop\ShippingBundle\Form\Type
 */
class ShippingMethodCategoryType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('categoryIds', 'weasty_catalog_category_id', array(
                'required' => false,
                'multiple' => true,
                'label' => 'Категории',
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
        return 'shipping_category';
    }

} 