<?php
namespace Shop\DiscountBundle\Form\Type;

use Shop\DiscountBundle\Data\ActionConditionResource;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ActionConditionType
 * @package Shop\DiscountBundle\Form\Type
 */
class ActionConditionType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder
            ->add('type', 'choice', array(
                'choices' => ActionConditionResource::$types,
                'label' => 'Объедининие',
            ))
            ->add('categories', 'entity', array(
                'required' => false,
                'multiple' => true,
                'label' => 'Участвующие категории',
                'help' => 'Выберите категории, для которых при заказе товаров или услуг клиенту будет показываться акция',
                'class' => 'ShopCatalogBundle:Category',
            ))
            ->add('proposals', 'weasty_admin_browser_type', array(
                'required' => false,
                'label' => 'Участвующие товары',
                'help' => 'Выберите товары и услуги при заказе которых клиенту будет показываться акция',
                'browser_path' => 'proposals_browser',
                'item_value_element_class' => 'item-value-element',
                'options' => array(
                    'class' => 'ShopCatalogBundle:Proposal',
                ),
                'prototype_options' => array(
                    'attr' => array(
                        'class' => 'item-value-element',
                    ),
                ),
                'allow_add' => true,
                'allow_delete' => true,
            ))
            ->add('discountType', 'choice', array(
                'choices' => ActionConditionResource::$discountTypes,
                'label' => 'Тип акции',
            ))
            ->add('discountPercent', 'text', array(
                'required' => false,
                'label' => 'Процент скидки',
            ))
            ->add('discountPrice', 'weasty_money_price', array(
                'value_options' => array(
                    'required' => false,
                    'label' => 'Акционная цена',
                ),
                'currency_form_type' => 'weasty_money_currency_numeric',
            ))
            ->add('discountCategories', 'entity', array(
                'required' => false,
                'multiple' => true,
                'label' => 'Категории, на которые растространяется акция (По умолчанию: все участвующие)',
                'help' => 'Выберите категории для которых будут действовать условия акции (скидка, акционная цена, подарок)',
                'class' => 'ShopCatalogBundle:Category',
            ))
            ->add('discountProposals', 'weasty_admin_browser_type', array(
                'required' => false,
                'label' => 'Товары, на которые растространяется акция (По умолчанию: все участвующие)',
                'help' => 'Выберите товары и услуги для которых будут действовать условия акции (скидка, акционная цена, подарок)',
                'browser_path' => 'proposals_browser',
                'type' => 'shop_discount_action_condition_proposal',
                'prototype_type' => 'shop_discount_action_condition_proposal',
                'item_value_element_class' => 'item-value-element',
                'allow_add' => true,
                'allow_delete' => true,
            ))
//            ->add('discountProposals', 'weasty_admin_browser_type', array(
//                'required' => false,
//                'label' => 'Товары, на которые растространяется акция (По умолчанию: все участвующие)',
//                'browser_path' => 'proposals_browser',
//                'options' => array(
//                    'class' => 'ShopCatalogBundle:Proposal',
//                ),
//                'allow_add' => true,
//                'allow_delete' => true,
//            ))
            ->add('gifts', 'weasty_admin_browser_type', array(
                'required' => false,
                'label' => 'Возможные подарки',
                'browser_path' => 'proposals_browser',
                'item_value_element_class' => 'item-value-element',
                'options' => array(
                    'class' => 'ShopCatalogBundle:Proposal',
                ),
                'prototype_options' => array(
                    'attr' => array(
                        'class' => 'item-value-element',
                    ),
                ),
                'allow_add' => true,
                'allow_delete' => true,
            ))
            ->add('priority', 'integer', array(
                'required' => false,
                'label' => 'Приоритет',
            ))
            ->add('isComplex', 'choice', array(
                'choices' => array(
                    'Нет',
                    'Да',
                ),
                'label' => 'Применяется с другими акциями в комплексе',
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
        return 'shop_discount_action_condition';
    }

} 