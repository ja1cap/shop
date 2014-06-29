<?php
namespace Shop\ShippingBundle\Calculator;
use Doctrine\Common\Util\Inflector;

/**
 * Class CategoryShippingSummary
 * @package Shop\ShippingBundle\Calculator
 */
class CategoryShippingSummary implements ShippingSummaryInterface, \ArrayAccess {

    /**
     * @var \Weasty\Bundle\CatalogBundle\Data\CategoryInterface
     */
    protected $category;

    /**
     * @var \Weasty\Money\Price\PriceInterface|null
     */
    protected $summaryPrice;

    /**
     * @var \Weasty\Money\Price\PriceInterface|null
     */
    protected $shippingPrice;

    /**
     * @var \Weasty\Money\Price\PriceInterface|null
     */
    protected $liftingPrice;

    /**
     * @var \Weasty\Money\Price\PriceInterface|null
     */
    protected $assemblyPrice;

    function __construct($category)
    {
        $this->category = $category;
    }

    /**
     * @return \Weasty\Bundle\CatalogBundle\Data\CategoryInterface
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @return null|\Weasty\Money\Price\PriceInterface
     */
    public function getAssemblyPrice()
    {
        return $this->assemblyPrice;
    }

    /**
     * @param null|\Weasty\Money\Price\PriceInterface $assemblyPrice
     */
    public function setAssemblyPrice($assemblyPrice)
    {
        $this->assemblyPrice = $assemblyPrice;
    }

    /**
     * @return null|\Weasty\Money\Price\PriceInterface
     */
    public function getLiftingPrice()
    {
        return $this->liftingPrice;
    }

    /**
     * @param null|\Weasty\Money\Price\PriceInterface $liftingPrice
     */
    public function setLiftingPrice($liftingPrice)
    {
        $this->liftingPrice = $liftingPrice;
    }

    /**
     * @return null|\Weasty\Money\Price\PriceInterface
     */
    public function getShippingPrice()
    {
        return $this->shippingPrice;
    }

    /**
     * @param null|\Weasty\Money\Price\PriceInterface $shippingPrice
     */
    public function setShippingPrice($shippingPrice)
    {
        $this->shippingPrice = $shippingPrice;
    }

    /**
     * @return null|\Weasty\Money\Price\PriceInterface
     */
    public function getSummaryPrice()
    {
        return $this->summaryPrice;
    }

    /**
     * @param null|\Weasty\Money\Price\PriceInterface $summaryPrice
     */
    public function setSummaryPrice($summaryPrice)
    {
        $this->summaryPrice = $summaryPrice;
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
    {
        $method = 'set' . Inflector::classify($offset);
        if(method_exists($this, $method)){
            $this->$method($value);
        }
    }

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
    {
        $method = 'set' . Inflector::classify($offset);
        if(method_exists($this, $method)){
            $this->$method(null);
        }
    }

    /**
     * @param $name
     * @return mixed
     */
    function __get($name)
    {

        if(strpos($name, '(') !== false){

            list($property, $argumentsList) = explode('(', $name);

            $method = 'get' . Inflector::classify($property);
            $arguments = explode(',', str_replace(')', '', $argumentsList));

            if(method_exists($this, $method)){
                return call_user_func_array(array($this, $method), $arguments);
            } else {
                return null;
            }

        } else {
            return $this->offsetGet($name);
        }
    }

}