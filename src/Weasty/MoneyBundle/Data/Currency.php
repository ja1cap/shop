<?php
namespace Weasty\MoneyBundle\Data;

use Doctrine\Common\Util\Inflector;

/**
 * Class Currency
 * @package Weasty\MoneyBundle\Data
 */
class Currency
implements CurrencyInterface, \ArrayAccess
{

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $symbol;

    /**
     * @var integer|string
     */
    protected $numericCode;

    /**
     * @var string
     */
    protected $alphabeticCode;

    /**
     * @param array $data
     */
    function __construct(array $data = array())
    {
        foreach($data as $key => $value){
            $this->offsetSet($key, $value);
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getSymbol()
    {
        return $this->symbol;
    }

    /**
     * @return integer|string
     */
    public function getNumericCode()
    {
        return $this->numericCode;
    }

    /**
     * @return integer|string
     */
    public function getAlphabeticCode()
    {
        return $this->alphabeticCode;
    }

    /**
     * @param string $alphabeticCode
     */
    public function setAlphabeticCode($alphabeticCode)
    {
        $this->alphabeticCode = $alphabeticCode;
    }

    /**
     * @param int|string $numericCode
     */
    public function setNumericCode($numericCode)
    {
        $this->numericCode = $numericCode;
    }

    /**
     * @param string $symbol
     */
    public function setSymbol($symbol)
    {
        $this->symbol = $symbol;
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
     * @return string
     */
    function __toString()
    {
        return $this->getName();
    }

} 