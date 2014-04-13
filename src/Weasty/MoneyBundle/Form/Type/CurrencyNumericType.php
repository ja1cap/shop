<?php
namespace Weasty\MoneyBundle\Form\Type;

use Weasty\MoneyBundle\Data\CurrencyResource;

/**
 * Class CurrencyNumericType
 * @package Weasty\MoneyBundle\Form\Type
 */
class CurrencyNumericType extends AbstractCurrencyType {

    /**
     * @return string
     */
    public function getName()
    {
        return 'weasty_money_currency_numeric';
    }

    /**
     * @return string
     */
    public function getCodeType()
    {
        return CurrencyResource::CODE_TYPE_ISO_4217_NUMERIC;
    }

} 