<?php
namespace Shop\CatalogBundle\Form\Type;

use Doctrine\Common\Collections\ArrayCollection;
use Shop\CatalogBundle\Entity\ContractorCurrency;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Shop\CatalogBundle\Entity\Contractor;

/**
 * Class ContractorCurrencyType
 * @package Shop\CatalogBundle\Form\Type
 */
class ContractorCurrencyType extends AbstractType {

    /**
     * @var Contractor
     */
    private $contractor;

    /**
     * @var ContractorCurrency
     */
    private $currency;

    /**
     * @param Contractor $contractor
     * @param ContractorCurrency $currency
     */
    function __construct(Contractor $contractor, ContractorCurrency $currency)
    {
        $this->contractor = $contractor;
        $this->currency = $currency;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $currencies = new ArrayCollection(ContractorCurrency::$currencyNames);
        $currencyCurrencyNumericCode = $this->currency->getNumericCode();

        if($currencyCurrencyNumericCode != $this->contractor->getDefaultCurrencyNumericCode()){
            $currencies->remove($this->contractor->getDefaultCurrencyNumericCode());
        }

        $this->contractor->getCurrencies()->map(function(ContractorCurrency $currency) use($currencies, $currencyCurrencyNumericCode) {
            if($currencyCurrencyNumericCode != $currency->getNumericCode()){
                $currencies->remove($currency->getNumericCode());
            }
        });

        $builder
            ->add('numericCode', 'choice', array(
                'choices' => $currencies->toArray(),
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