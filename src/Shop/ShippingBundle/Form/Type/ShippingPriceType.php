<?php
namespace Shop\ShippingBundle\Form\Type;

use Shop\ShippingBundle\Entity\ShippingPrice;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ShippingPriceType
 * @package Shop\ShippingBundle\Form\Type
 */
class ShippingPriceType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('orderPriceType', 'choice', array(
                'expanded' => true,
                'label' => 'Сумма заказа',
                'choices' => array(
                    ShippingPrice::ORDER_PRICE_TYPE_ANY => 'любая',
                    ShippingPrice::ORDER_PRICE_TYPE_RANGE => 'диапазон',
                ),
            ))
            ->add('minOrderPrice', 'weasty_money_price', array(
                'value_options' => array(
                    'required' => false,
                    'label' => 'Мин.сумма заказа',
                    'attr' => array(
                        'placeholder' => ''
                    ),
                ),
                'currency_form_type' => 'weasty_money_currency_numeric',
            ))
            ->add('maxOrderPrice', 'weasty_money_price', array(
                'value_options' => array(
                    'required' => false,
                    'label' => 'Макс.сумма заказа',
                    'attr' => array(
                        'placeholder' => ''
                    ),
                ),
                'currency_form_type' => 'weasty_money_currency_numeric',
            ))
            ->add('price', 'weasty_money_price', array(
                'value_options' => array(
                    'required' => false,
                    'label' => 'Стоимость доставки',
                    'attr' => array(
                        'placeholder' => 'Поумолчанию бесплатно'
                    ),
                ),
                'currency_form_type' => 'weasty_money_currency_numeric',
            ))
            ->add('liftingType', 'choice', array(
                'label' => 'Подъем на этаж',
                'choices' => ShippingPrice::$liftingTypes,
            ))
            ->add('assemblyType', 'choice', array(
                'label' => 'Сборка',
                'choices' => ShippingPrice::$assemblyTypes,
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
        return 'shipping_price';
    }

} 