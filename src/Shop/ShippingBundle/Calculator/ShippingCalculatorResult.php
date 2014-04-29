<?php
namespace Shop\ShippingBundle\Calculator;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Util\Inflector;
use Weasty\MoneyBundle\Data\Price;

/**
 * Class ShippingCalculatorResult
 * @package Shop\ShippingBundle\Calculator
 */
class ShippingCalculatorResult
    implements ShippingCalculatorResultInterface, \IteratorAggregate, \ArrayAccess
{

    /**
     * @var \Weasty\GeonamesBundle\Entity\City
     */
    protected $city;

    /**
     * @var int
     */
    protected $liftType;

    /**
     * @var int
     */
    protected $floor;

    /**
     * @var ArrayCollection
     */
    protected $shippingSummaries;

    function __construct()
    {
        $this->shippingSummaries = new ArrayCollection();
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return \Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     */
    public function getIterator()
    {
        return $this->shippingSummaries->getIterator();
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function addShippingSummary($value)
    {
        if(!$value instanceof ShippingSummaryInterface){
            return false;
        }
        return $this->shippingSummaries->add($value);
    }

    /**
     * @param int|string $key
     * @param mixed $value
     */
    public function setShippingSummary($key, $value)
    {
        if($value instanceof ShippingSummaryInterface){
            $this->shippingSummaries->set($key, $value);
        }
    }

    /**
     * @return ArrayCollection
     */
    public function getShippingSummaries()
    {
        return $this->shippingSummaries;
    }

    /**
     * @return Price
     */
    public function getSummaryPrice(){

        $priceValue = 0;
        $priceCurrency = null;

        foreach($this->shippingSummaries->toArray() as $shippingSummary){
            if($shippingSummary instanceof ShippingSummaryInterface){
                $priceValue += $shippingSummary->getSummaryPrice()->getValue();
                $priceCurrency = $shippingSummary->getSummaryPrice()->getCurrency();
            }
        }

        return new Price($priceValue, $priceCurrency);

    }

    /**
     * @return \Weasty\GeonamesBundle\Entity\City
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param \Weasty\GeonamesBundle\Entity\City $city
     * @return $this
     */
    public function setCity($city)
    {
        $this->city = $city;
        return $this;
    }

    /**
     * @return int
     */
    public function getFloor()
    {
        return $this->floor;
    }

    /**
     * @param int $floor
     * @return $this
     */
    public function setFloor($floor)
    {
        $this->floor = $floor;
        return $this;
    }

    /**
     * @return int
     */
    public function getLiftType()
    {
        return $this->liftType;
    }

    /**
     * @param int $liftType
     * @return $this
     */
    public function setLiftType($liftType)
    {
        $this->liftType = $liftType;
        return $this;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        $method = 'get' . Inflector::classify($offset);
        return method_exists($this, $method);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        $method = 'get' . Inflector::classify($offset);
        if(method_exists($this, $method)){
            return $this->$method();
        }
        return null;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     */
    public function offsetSet($offset, $value)
    {}

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     */
    public function offsetUnset($offset)
    {}

}