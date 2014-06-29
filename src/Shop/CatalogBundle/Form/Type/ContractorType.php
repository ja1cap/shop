<?php
namespace Shop\CatalogBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ContractorType
 * @package Shop\CatalogBundle\Form\Type
 */
class ContractorType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('name', 'text', array(
                'required' => true,
                'label' => 'Название',
            ));

        $builder
            ->add('defaultCurrencyNumericCode', 'weasty_money_currency_numeric', array(
                'required' => true,
                'label' => 'Валюта поумолчанию',
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
        return 'contractor';
    }

} 