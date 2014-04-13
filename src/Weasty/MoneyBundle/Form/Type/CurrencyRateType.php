<?php
namespace Weasty\MoneyBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Weasty\MoneyBundle\Data\CurrencyResource;

/**
 * Class CurrencyRateType
 * @package Weasty\MoneyBundle\Form
 */
class CurrencyRateType extends AbstractType {

    /**
     * @var CurrencyResource
     */
    protected $currencyResource;

    /**
     * @param CurrencyResource $currencyResource
     */
    function __construct(CurrencyResource $currencyResource)
    {
        $this->currencyResource = $currencyResource;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('sourceAlphabeticCode', 'weasty_money_currency', array(
                'label' => 'Конвертируемая валюта',
            ))
            ->add('destinationAlphabeticCode', 'weasty_money_currency', array(
                'label' => 'Базовая валюта'
            ))
            ->add('rate', 'number', array(
                'label' => 'Курс конверсии'
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
        return 'weasty_money_currency_rate';
    }

    /**
     * @return CurrencyResource
     */
    public function getCurrencyResource()
    {
        return $this->currencyResource;
    }

} 