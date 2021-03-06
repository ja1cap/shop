<?php
namespace Weasty\Bundle\CatalogBundle\Feature;

/**
 * Class FeaturesResource
 * @package Weasty\Bundle\CatalogBundle\Feature
 */
class FeaturesResource implements FeaturesResourceInterface {

    /**
     * @var FeatureInterface[]
     */
    public $features;

    /**
     * @var FeatureGroupInterface[]
     */
    public $groups;

    function __construct()
    {
        $this->features = [];
        $this->groups = [];
    }

    /**
     * @return FeatureInterface[]
     */
    public function getFeatures()
    {
        return $this->features;
    }

    /**
     * @param int $id
     * @return FeatureInterface|null
     */
    public function getFeature($id){
        return (isset($this->features[$id]) ? $this->features[$id] : null);
    }

    /**
     * @param FeatureInterface $feature
     * @return $this
     */
    public function addFeature(FeatureInterface $feature){
        $this->features[$feature->getId()] = $feature;
        return $this;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function removeFeature($id){
        if(isset($this->features[$id])){
            unset($this->features[$id]);
        }
        return $this;
    }

    /**
     * @return FeatureGroup[]
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @param FeatureGroupInterface $featureGroup
     * @return $this
     */
    public function addGroup(FeatureGroupInterface $featureGroup)
    {
        $this->groups[$featureGroup->getId()] = $featureGroup;
        return $this;
    }

    /**
     * @param int $id
     * @return FeatureGroupInterface|null
     */
    public function getGroup($id)
    {
        return (isset($this->groups[$id]) ? $this->groups[$id] : null);
    }

    /**
     * @param int $id
     * @return $this
     */
    public function removeGroup($id)
    {
        if(isset($this->groups[$id])){
            unset($this->groups[$id]);
        }
        return $this;
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