<?php
namespace Shop\CatalogBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ContractorCurrencyType
 * @package Shop\CatalogBundle\Form\Type
 */
class ContractorCurrencyType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('numericCode', 'weasty_money_currency_numeric', array(
                'required' => true,
                'label' => 'Валюта',
            ));

        $builder
            ->add('value', 'text', array(
                'required' => true,
                'label' => 'Курс',
            ));

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
        return 'contractor_currency';
    }

} 