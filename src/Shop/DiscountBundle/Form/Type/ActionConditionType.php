<?php
namespace Shop\DiscountBundle\Form\Type;

use Shop\DiscountBundle\ActionCondition\ActionConditionData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ActionConditionType
 * @package Shop\DiscountBundle\Form\Type
 */
abstract class ActionConditionType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    protected function buildConditionForm(FormBuilderInterface $builder, array $options){}

    /**
     * @return array
     */
    protected function getConditionTypeChoices(){
        return ActionConditionData::getTypes();
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $this->buildConditionForm($builder, $options);

        $builder
            ->add('type', 'choice', array(
                'choices' => $this->getConditionTypeChoices(),
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
            ->add('giftProposals', 'weasty_admin_browser_type', array(
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
//            ->add('priority', 'hidden', array(
//                'required' => false,
//                'label' => 'Приоритет',
//            ))
//            ->add('isComplex', 'checkbox', array(
//                'required' => false,
//                'label' => 'Применяется с другими акциями в комплексе',
//            ))
        ;

        $builder
            ->add('save', 'submit', array(
                'label' => 'Сохранить',
            ))
        ;

    }

}