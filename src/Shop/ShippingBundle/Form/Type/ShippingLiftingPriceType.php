<?php
namespace Shop\ShippingBundle\Form\Type;

use Shop\ShippingBundle\Entity\ShippingLiftingPrice;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ShippingLiftingPriceType
 * @package Shop\ShippingBundle\Form\Type
 */
class ShippingLiftingPriceType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('priceType', 'choice', array(
                'expanded' => true,
                'label' => 'Тип цены',
                'choices' => array(
                    ShippingLiftingPrice::PRICE_TYPE_ANY_FLOOR => 'подъем на любой этаж',
                    ShippingLiftingPrice::PRICE_TYPE_PER_FLOOR => 'цена за этаж',
                ),
            ))
            ->add('noLiftPrice', 'weasty_money_price', array(
                'value_options' => array(
                    'required' => false,
                    'label' => 'Стоимость без лифта',
                    'attr' => array(
                        'placeholder' => 'Поумолчанию бесплатно'
                    ),
                ),
                'currency_form_type' => 'weasty_money_currency_numeric',
            ))
            ->add('liftPrice', 'weasty_money_price', array(
                'value_options' => array(
                    'required' => false,
                    'label' => 'Стоимость на пассажирском лифта',
                    'attr' => array(
                        'placeholder' => 'Поумолчанию бесплатно'
                    ),
                ),
                'currency_form_type' => 'weasty_money_currency_numeric',
            ))
            ->add('serviceLiftPrice', 'weasty_money_price', array(
                'value_options' => array(
                    'required' => false,
                    'label' => 'Стоимость на грузовом лифта',
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
        return 'shipping_lifting_price';
    }

} 