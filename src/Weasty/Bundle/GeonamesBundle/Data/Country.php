<?php
namespace Weasty\Bundle\GeonamesBundle\Data;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Inflector\Inflector;
use JJs\Bundle\GeonamesBundle\Data\Country as BaseCountry;
use Traversable;

/**
 * Class Country
 * @package Weasty\Bundle\GeonamesBundle\Data
 */
class Country extends BaseCountry implements \ArrayAccess, \IteratorAggregate {

    /**
     * @var mixed
     */
    public $capital;

    /**
     * @var integer
     */
    public $geonameIdentifier;

    public function __construct()
    {}

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     */
    public function getIterator()
    {

        $iterator = new ArrayCollection();

        foreach(get_class_vars(__CLASS__) as $key => $value){
            $iterator->set($key, $this->offsetGet($key));
        }

        return $iterator;

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
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @param string $domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param string $phonePrefix
     */
    public function setPhonePrefix($phonePrefix)
    {
        $this->phonePrefix = $phonePrefix;
    }

    /**
     * @param string $postalCodeFormat
     */
    public function setPostalCodeFormat($postalCodeFormat)
    {
        $this->postalCodeFormat = $postalCodeFormat;
    }

    /**
     * @param string $postalCodeRegex
     */
    public function setPostalCodeRegex($postalCodeRegex)
    {
        $this->postalCodeRegex = $postalCodeRegex;
    }

    /**
     * @param int $geonameIdentifier
     * @return $this
     */
    public function setGeonameIdentifier($geonameIdentifier)
    {
        $this->geonameIdentifier = $geonameIdentifier;
        return $this;
    }

    /**
     * @return int
     */
    public function getGeonameIdentifier()
    {
        return $this->geonameIdentifier;
    }

    /**
     * @return mixed
     */
    public function getCapital()
    {
        return $this->capital;
    }

    /**
     * @param mixed $capital
     */
    public function setCapital($capital)
    {
        $this->capital = $capital;
    }

} 