<?php
namespace Shop\MainBundle\Data;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\Common\Util\Inflector;

/**
 * Class ShopContactsResource
 * @package Shop\MainBundle\Data
 */
class ShopContactsResource implements \ArrayAccess {

    /**
     * @var ShopSettingsResource
     */
    protected $settingsResource;

    /**
     * @var ObjectRepository
     */
    protected $addressRepository;

    /**
     * @var array
     */
    protected $addresses;

    /**
     * @var ObjectRepository
     */
    protected $phoneRepository;

    /**
     * @var array
     */
    protected $phones;

    /**
     * @param ShopSettingsResource $settingsResource
     * @param ObjectRepository $addressRepository
     * @param ObjectRepository $phoneRepository
     */
    function __construct(ShopSettingsResource $settingsResource, ObjectRepository $addressRepository, ObjectRepository $phoneRepository)
    {
        $this->settingsResource = $settingsResource;
        $this->addressRepository = $addressRepository;
        $this->phoneRepository = $phoneRepository;
    }

    /**
     * @return array
     */
    public function getAddresses()
    {
        if($this->addresses === null){
            $this->addresses = $this->addressRepository->findAll();
        }
        return $this->addresses;
    }

    /**
     * @return array
     */
    public function getPhones()
    {
        if($this->phones === null){
            $this->phones = $this->phoneRepository->findAll();
        }
        return $this->phones;
    }

    /**
     * @return string|null
     */
    public function getEmail(){
        return $this->settingsResource->getSettings()->getEmail();
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