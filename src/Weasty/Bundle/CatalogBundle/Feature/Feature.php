<?php
namespace Weasty\Bundle\CatalogBundle\Feature;

/**
 * Class Feature
 * @package Weasty\Bundle\CatalogBundle\Feature
 */
class Feature implements FeatureInterface {

    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var \Weasty\Bundle\CatalogBundle\Feature\FeatureValueInterface[]
     */
    public $featureValues = [];

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
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
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param $key
     * @param \Weasty\Bundle\CatalogBundle\Feature\FeatureValueInterface $value
     * @return $this
     */
    public function addFeatureValue($key, $value){
        $this->featureValues[$key] = $value;
        return $this;
    }

    /**
     * @param $key
     * @return $this
     */
    public function removeFeatureValue($key){
        if(isset($this->featureValues[$key])){
            unset($this->featureValues[$key]);
        }
        return $this;
    }

    /**
     * @param $key
     * @return \Weasty\Bundle\CatalogBundle\Feature\FeatureValueInterface|null
     */
    public function getFeatureValue($key){
        if(isset($this->featureValues[$key])){
            return $this->featureValues[$key];
        }
        return null;
    }

    /**
     * @return \Weasty\Bundle\CatalogBundle\Feature\FeatureValueInterface[]
     */
    public function getFeatureValues(){
        return $this->featureValues;
    }

    /**
     * @return FeatureValueInterface[]
     */
    public function getValues(){
        return $this->featureValues;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {

        if(!$this->getFeatureValues()){
            return null;
        }

        return implode(', ',
            array_map(
                function($value){
                    return (string)$value;

                },
                $this->getFeatureValues()
            )
        );

    }

    /**
     * @return array
     */
    public function toArray()
    {
        return get_object_vars($this);
    }

    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     */
    function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName() . ' - ' . $this->getValue();
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
        $method = 'get' . str_replace(" ", "", ucwords(strtr($offset, "_-", "  ")));
        if(method_exists($this, $method)){
            return $this->$method();
        }
        return null;
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
        $method = 'get' . str_replace(" ", "", ucwords(strtr($offset, "_-", "  ")));
        return method_exists($this, $method);
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
        $method = 'set' . str_replace(" ", "", ucwords(strtr($offset, "_-", "  ")));
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
        $method = 'set' . str_replace(" ", "", ucwords(strtr($offset, "_-", "  ")));
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

            $method = 'get' . str_replace(" ", "", ucwords(strtr($property, "_-", "  ")));
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