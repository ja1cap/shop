<?php
namespace Shop\ShippingBundle\Mapper;
use Doctrine\Common\Util\Inflector;
use Shop\ShippingBundle\Entity\ShippingAssemblyPrice;

/**
 * Class ShippingAssemblyPriceMapper
 * @package Shop\ShippingBundle\Mapper
 */
class ShippingAssemblyPriceMapper {

    /**
     * @var ShippingAssemblyPrice
     */
    protected $shippingAssemblyPrice;

    /**
     * @param ShippingAssemblyPrice $shippingAssemblyPrice
     */
    function __construct(ShippingAssemblyPrice $shippingAssemblyPrice)
    {
        $this->shippingAssemblyPrice = $shippingAssemblyPrice;
    }

    /**
     * @return array
     */
    public function getPrice(){
        return array(
            'value' => $this->getShippingAssemblyPrice()->getValue(),
            'currency' => $this->getShippingAssemblyPrice()->getCurrencyNumericCode(),
        );
    }

    /**
     * @param $price
     * @return $this
     */
    public function setPrice($price){

        $this->getShippingAssemblyPrice()
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
        return $this->getShippingAssemblyPrice()->offsetGet($name);
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
        $this->getShippingAssemblyPrice()->offsetSet($name, $value);
    }

    /**
     * @return ShippingAssemblyPrice
     */
    public function getShippingAssemblyPrice()
    {
        return $this->shippingAssemblyPrice;
    }

} 