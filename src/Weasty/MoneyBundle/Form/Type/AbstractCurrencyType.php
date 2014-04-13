<?php
namespace Weasty\MoneyBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\CurrencyType as BaseCurrencyType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Weasty\MoneyBundle\Converter\CurrencyCodeConverter;
use Weasty\MoneyBundle\Data\CurrencyResource;

/**
 * Class AbstractCurrencyType
 * @package Weasty\MoneyBundle\Form\Type
 */
abstract class AbstractCurrencyType extends BaseCurrencyType {


    /**
     * @var CurrencyResource
     */
    protected $currencyResource;

    /**
     * @var \Weasty\MoneyBundle\Converter\CurrencyCodeConverter
     */
    protected $currencyCodeConverter;

    function __construct(CurrencyResource $currencyResource, CurrencyCodeConverter $currencyCodeConverter)
    {
        $this->currencyResource = $currencyResource;
        $this->currencyCodeConverter = $currencyCodeConverter;
    }

    /**
     * @return string
     */
    abstract public function getCodeType();

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {

        $choices = array();

        foreach($this->getCurrencyResource()->getCurrencies() as $alphabeticCode => $currency){
            $choices[$this->getCurrencyCodeConverter()->convert($alphabeticCode, $this->getCodeType(), CurrencyResource::CODE_TYPE_ISO_4217_ALPHABETIC)] = $currency;
        }

        $resolver->setDefaults(array(
            'choices' => $choices,
        ));

    }

    /**
     * @return CurrencyResource
     */
    public function getCurrencyResource()
    {
        return $this->currencyResource;
    }

    /**
     * @return CurrencyCodeConverter
     */
    public function getCurrencyCodeConverter()
    {
        return $this->currencyCodeConverter;
    }

} 