<?php

namespace Shop\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class CatalogAdmin
 * @package Shop\UserBundle\Entity
 */
class CatalogAdmin extends AbstractUser
{

    /**
     * User entity type from constants
     * @return int
     */
    function getType()
    {
        return self::TYPE_CATALOG_ADMIN;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        $this->roles[] = self::ROLE_CATALOG_ADMIN;
        return parent::getRoles();
    }

}
