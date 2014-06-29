<?php

namespace Shop\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Courier
 * @package Shop\UserBundle\Entity
 */
class Courier extends AbstractUser
{

    /**
     * User entity type from constants
     * @return int
     */
    function getType()
    {
        return self::TYPE_COURIER;
    }

}
