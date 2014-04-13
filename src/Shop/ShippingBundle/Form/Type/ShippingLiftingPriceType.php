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
            ->add('floorAmountType', 'choice', array(
                'expanded' => true,
                'label' => 'Этаж',
                'choices' => array(
                    ShippingLiftingPrice::FLOOR_AMOUNT_TYPE_ANY => 'любой',
                    ShippingLiftingPrice::FLOOR_AMOUNT_TYPE_RANGE => 'диапазон',
                ),
            ))
            ->add('minFloor', 'text', array(
                'required' => false,
                'label' => 'Начальный этаж',
            ))
            ->add('maxFloor', 'text', array(
                'required' => false,
                'label' => 'Конечный этаж',
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