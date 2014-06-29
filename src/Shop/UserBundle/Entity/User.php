<?php

namespace Shop\UserBundle\Entity;

/**
 * Class User
 * @package Shop\UserBundle\Entity
 */
class User extends AbstractUser
{

    /**
     * User entity type from constants
     * @return int
     */
    function getType()
    {
        return self::TYPE_USER;
    }

}
