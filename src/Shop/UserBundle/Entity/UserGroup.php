<?php

namespace Shop\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Util\Inflector;
use FOS\UserBundle\Model\Group;

/**
 * Class UserGroup
 * @package Shop\UserBundle\Entity
 */
class UserGroup extends Group
    implements \ArrayAccess
{

    const SLUG_USERS = 'users';
    const SLUG_ADMINS = 'admins';
    const SLUG_CATALOG_ADMINS = 'catalog_admins';
    const SLUG_MANAGERS = 'managers';
    const SLUG_ACCOUNTANTS = 'accountants';

    /**
     * @var integer
     */
    protected  $id;

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
     * @var string
     */
    private $slug;


    /**
     * Set slug
     *
     * @param string $slug
     * @return UserGroup
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @return string
     */
    public function getRoutePrefix(){

        switch($this->getSlug()){

            case self::SLUG_ADMINS:
                return $this->getSlug();
            case self::SLUG_MANAGERS:
                return $this->getSlug();
            default:
                return self::SLUG_USERS;

        }

    }

    /**
     * @return string
     */
    public function getUserClassName(){

        $className = '\Shop\UserBundle\Entity\User';

        switch($this->getSlug()){
            case self::SLUG_ADMINS:
                $className = '\Shop\UserBundle\Entity\Admin';
                break;
            case self::SLUG_MANAGERS:
                $className = '\Shop\UserBundle\Entity\Manager';
                break;
            case self::SLUG_CATALOG_ADMINS:
                $className = '\Shop\UserBundle\Entity\CatalogAdmin';
                break;
            case self::SLUG_ACCOUNTANTS:
                $className = '\Shop\UserBundle\Entity\Accountant';
                break;
        }

        return $className;

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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $users;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    /**
     * Add users
     *
     * @param \Shop\UserBundle\Entity\AbstractUser $users
     * @return UserGroup
     */
    public function addUser(AbstractUser $users)
    {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \Shop\UserBundle\Entity\AbstractUser $users
     */
    public function removeUser(AbstractUser $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
    }
}
