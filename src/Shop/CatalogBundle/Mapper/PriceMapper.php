<?php
namespace Shop\CatalogBundle\Mapper;

use Shop\CatalogBundle\Entity\Price;
use Doctrine\Common\Util\Inflector;

/**
 * Class PriceMapper
 * @package Shop\CatalogBundle\Mapper
 */
class PriceMapper {

    /**
     * @var Price
     */
    protected $entity;

    /**
     * @param Price $entity
     */
    function __construct(Price $entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return array
     */
    public function getPrice(){
        return array(
            'value' => $this->getEntity()->getValue(),
            'currency' => $this->getEntity()->getCurrencyNumericCode(),
        );
    }

    /**
     * @param $data
     * @return $this
     */
    public function setPrice($data){

        $this->getEntity()
            ->setValue($data['value'])
            ->setCurrencyNumericCode($data['currency'])
        ;

        return $this;

    }

    /**
     * @return Price
     */
    public function getEntity()
    {
        return $this->entity;
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
        return $this->getEntity()->offsetGet($name);
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
        $this->getEntity()->offsetSet($name, $value);
    }

} 