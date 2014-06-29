<?php

namespace Shop\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Admin
 * @package Shop\UserBundle\Entity
 */
class Admin extends AbstractUser
{

    /**
     * User entity type from constants
     * @return int
     */
    function getType()
    {
        return self::TYPE_ADMIN;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        $this->roles[] = self::ROLE_ADMIN;
        return parent::getRoles();
    }

}
