<?php
namespace Shop\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Inflector\Inflector;
use FOS\UserBundle\Model\GroupInterface;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * Class AbstractUser
 * @package Shop\UserBundle\Entity
 */
abstract class AbstractUser
    extends BaseUser
    implements \ArrayAccess
{

    const TYPE_USER = 1;
    const TYPE_MANAGER = 2;
    const TYPE_ADMIN = 3;
    const TYPE_COURIER = 4;
    const TYPE_CATALOG_ADMIN = 5;
    const TYPE_ACCOUNTANT = 6;

    const ROLE_MANAGER = 'ROLE_MANAGER';
    const ROLE_CATALOG_ADMIN = 'ROLE_CATALOG_ADMIN';
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_ACCOUNTANT = 'ROLE_ACCOUNTANT';

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $groups;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->groups = new ArrayCollection();
        parent::__construct();
    }

    /**
     * User entity type from constants
     * @return int
     */
    abstract function getType();

    /**
     * @param string $password
     * @return $this
     */
    public function setPlainPassword($password)
    {
        if($password){
            return parent::setPlainPassword($password);
        }
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param GroupInterface $group
     * @return $this
     */
    public function addGroup(GroupInterface $group)
    {
        return parent::addGroup($group);
    }

    /**
     * @param GroupInterface $group
     * @return $this
     */
    public function removeGroup(GroupInterface $group)
    {
        return parent::removeGroup($group);
    }

    /**
     * Get groups
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGroups()
    {
        return $this->groups;
    }

}
