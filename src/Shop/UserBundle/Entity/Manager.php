<?php

namespace Shop\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Manager
 * @package Shop\UserBundle\Entity
 */
class Manager extends AbstractUser
{

    /**
     * User entity type from constants
     * @return int
     */
    function getType()
    {
        return self::TYPE_MANAGER;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        $this->roles[] = self::ROLE_MANAGER;
        return parent::getRoles();
    }

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $contractors;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->contractors = new ArrayCollection();
        parent::__construct();
    }

    /**
     * Add contractors
     *
     * @param \Shop\UserBundle\Entity\ManagerContractor $contractor
     * @return Manager
     */
    public function addContractor(ManagerContractor $contractor)
    {
        $this->contractors[] = $contractor;
        $contractor->setManager($this);
        return $this;
    }

    /**
     * Remove contractors
     *
     * @param \Shop\UserBundle\Entity\ManagerContractor $contractors
     */
    public function removeContractor(ManagerContractor $contractors)
    {
        $this->contractors->removeElement($contractors);
    }

    /**
     * Get contractors
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getContractors()
    {
        return $this->contractors;
    }
}
