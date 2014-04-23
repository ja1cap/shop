<?php
namespace Shop\ShippingBundle\Calculator;

use Doctrine\Common\Collections\ArrayCollection;
use Weasty\MoneyBundle\Data\Price;

/**
 * Class ShippingSummary
 * @package Shop\ShippingBundle\Calculator
 */
class ShippingSummariesCollection extends ArrayCollection {

    /**
     * @param mixed $value
     * @return bool
     */
    public function add($value)
    {
        if(!$value instanceof ShippingSummaryInterface){
            return false;
        }
        return parent::add($value);
    }

    /**
     * @param int|string $key
     * @param mixed $value
     */
    public function set($key, $value)
    {
        if($value instanceof ShippingSummaryInterface){
            parent::set($key, $value);
        }
    }

    /**
     * @return Price
     */
    public function getSummaryPrice(){

        $priceValue = 0;
        $priceCurrency = null;

        foreach($this->toArray() as $shippingSummary){
            if($shippingSummary instanceof ShippingSummaryInterface){
                $priceValue += $shippingSummary->getSummaryPrice()->getValue();
                $priceCurrency = $shippingSummary->getSummaryPrice()->getCurrency();
            }
        }

        return new Price($priceValue, $priceCurrency);

    }

}