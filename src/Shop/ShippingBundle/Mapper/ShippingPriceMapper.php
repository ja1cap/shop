<?php
namespace Shop\ShippingBundle\Mapper;
use Doctrine\Common\Util\Inflector;
use Shop\ShippingBundle\Entity\ShippingPrice;

/**
 * Class ShippingPriceMapper
 * @package Shop\ShippingBundle\Mapper
 */
class ShippingPriceMapper {

    /**
     * @var ShippingPrice
     */
    protected $shippingPrice;

    /**
     * @param ShippingPrice $shippingPrice
     */
    function __construct(ShippingPrice $shippingPrice)
    {
        $this->shippingPrice = $shippingPrice;
    }

    /**
     * @return array
     */
    public function getMinOrderPrice(){
        return array(
            'value' => $this->getShippingPrice()->getMinOrderPriceValue(),
            'currency' => $this->getShippingPrice()->getMinOrderPriceCurrencyNumericCode(),
        );
    }

    /**
     * @param $price
     * @return $this
     */
    public function setMinOrderPrice($price){

        $this->getShippingPrice()
            ->setMinOrderPriceValue($price['value'])
            ->setMinOrderPriceCurrencyNumericCode($price['currency'])
        ;

        return $this;

    }

    /**
     * @return array
     */
    public function getMaxOrderPrice(){
        return array(
            'value' => $this->getShippingPrice()->getMaxOrderPriceValue(),
            'currency' => $this->getShippingPrice()->getMaxOrderPriceCurrencyNumericCode(),
        );
    }

    /**
     * @param $price
     * @return $this
     */
    public function setMaxOrderPrice($price){

        $this->getShippingPrice()
            ->setMaxOrderPriceValue($price['value'])
            ->setMaxOrderPriceCurrencyNumericCode($price['currency'])
        ;

        return $this;

    }

    /**
     * @return array
     */
    public function getPrice(){
        return array(
            'value' => $this->getShippingPrice()->getValue(),
            'currency' => $this->getShippingPrice()->getCurrencyNumericCode(),
        );
    }

    /**
     * @param $price
     * @return $this
     */
    public function setPrice($price){

        $this->getShippingPrice()
            ->setValue($price['value'])
            ->setCurrencyNumericCode($price['currency'])
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
        return $this->getShippingPrice()->offsetGet($name);
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
        $this->getShippingPrice()->offsetSet($name, $value);
    }

    /**
     * @return ShippingPrice
     */
    public function getShippingPrice()
    {
        return $this->shippingPrice;
    }

} 