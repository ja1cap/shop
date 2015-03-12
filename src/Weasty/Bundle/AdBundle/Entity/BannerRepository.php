<?php
namespace Weasty\Bundle\AdBundle\Entity;

use Weasty\Doctrine\Entity\AbstractRepository;

/**
 * Class BannerRepository
 * @package Weasty\Bundle\AdBundle\Entity
 */
class BannerRepository extends AbstractRepository {

    /**
     * @return \Weasty\Bundle\AdBundle\Banner\BannerInterface[]
     */
    public function getBanners(){
        return $this->findAll();
    }

}