<?php
namespace Shop\ShippingBundle\Mapper;

use Doctrine\Common\Util\Inflector;
use Shop\ShippingBundle\Entity\ShippingLiftingPrice;

/**
 * Class ShippingLiftingPriceMapper
 * @package Shop\ShippingBundle\Mapper
 */
class ShippingLiftingPriceMapper {

    /**
     * @var ShippingLiftingPrice
     */
    protected $liftingPrice;

    /**
     * @param ShippingLiftingPrice $liftingPrice
     */
    function __construct(ShippingLiftingPrice $liftingPrice)
    {
        $this->liftingPrice = $liftingPrice;
    }

    /**
     * @return array
     */
    public function getNoLiftPrice(){
        return array(
            'value' => $this->getLiftingPrice()->getNoLiftPriceValue(),
            'currency' => $this->getLiftingPrice()->getNoLiftPriceCurrencyNumericCode(),
        );
    }

    /**
     * @param $price
     * @return $this
     */
    public function setNoLiftPrice($price){

        $this->getLiftingPrice()
            ->setNoLiftPriceValue($price['value'])
            ->setNoLiftPriceCurrencyNumericCode($price['currency'])
        ;

        return $this;

    }

    /**
     * @return array
     */
    public function getLiftPrice(){
        return array(
            'value' => $this->getLiftingPrice()->getLiftPriceValue(),
            'currency' => $this->getLiftingPrice()->getLiftPriceCurrencyNumericCode(),
        );
    }

    /**
     * @param $price
     * @return $this
     */
    public function setLiftPrice($price){

        $this->getLiftingPrice()
            ->setLiftPriceValue($price['value'])
            ->setLiftPriceCurrencyNumericCode($price['currency'])
        ;

        return $this;

    }

    /**
     * @return array
     */
    public function getServiceLiftPrice(){
        return array(
            'value' => $this->getLiftingPrice()->getServiceLiftPriceValue(),
            'currency' => $this->getLiftingPrice()->getServiceLiftPriceCurrencyNumericCode(),
        );
    }

    /**
     * @param $price
     * @return $this
     */
    public function setServiceLiftPrice($price){

        $this->getLiftingPrice()
            ->setServiceLiftPriceValue($price['value'])
            ->setServiceLiftPriceCurrencyNumericCode($price['currency'])
        ;

        return $this;

    }

    /**
     * @param $name
     * @return mixed
     */
    function __get($name)
    {
        $method = 'get' . Inflector::classify($name);
        if(method_exists($this, $method)){
            return $this->$method();
        }
        return $this->getLiftingPrice()->offsetGet($name);
    }

    /**
     * @param $name
     * @param $value
     */
    function __set($name, $value)
    {
        $method = 'set' . Inflector::classify($name);
        if(method_exists($this, $method)){
            $this->$method($value);
        }
        $this->getLiftingPrice()->offsetSet($name, $value);
    }

    /**
     * @return ShippingLiftingPrice
     */
    public function getLiftingPrice()
    {
        return $this->liftingPrice;
    }

} 