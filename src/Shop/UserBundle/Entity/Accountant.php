<?php

namespace Shop\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Accountant
 * @package Shop\UserBundle\Entity
 */
class Accountant extends AbstractUser
{

    /**
     * User entity type from constants
     * @return int
     */
    function getType()
    {
        return self::TYPE_ACCOUNTANT;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        $this->roles[] = self::ROLE_ACCOUNTANT;
        return parent::getRoles();
    }

}
