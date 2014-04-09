<?php
namespace Shop\ShippingBundle\Form\Type;

use Shop\CatalogBundle\Entity\ContractorCurrency;
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
            ->add('minOrderPrice', 'text', array(
                'required' => false,
                'label' => 'Мин.сумма заказа',
            ))
            ->add('maxOrderPrice', 'text', array(
                'required' => false,
                'label' => 'Макс.сумма заказа',
            ))
            ->add('value', 'text', array(
                'required' => false,
                'label' => 'Стоимость доставки',
                'attr' => array(
                    'placeholder' => 'Поумолчанию бесплатно'
                ),
            ))
            ->add('currencyNumericCode', 'choice', array(
                'label' => 'Валюта',
                'choices' => ContractorCurrency::$currencyNames,
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