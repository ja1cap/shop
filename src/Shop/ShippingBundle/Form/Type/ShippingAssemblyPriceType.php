<?php
namespace Shop\ShippingBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ShippingAssemblyPriceType
 * @package Shop\ShippingBundle\Form\Type
 */
class ShippingAssemblyPriceType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('price', 'weasty_money_price', array(
                'value_options' => array(
                    'required' => false,
                    'label' => 'Стоимость сборки',
                    'attr' => array(
                        'placeholder' => 'Поумолчанию бесплатно'
                    ),
                ),
                'currency_form_type' => 'weasty_money_currency_numeric',
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
        return 'shipping_assembly_price';
    }

} 