<?php

namespace Weasty\Bundle\AdBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Banner
 * @package Weasty\Bundle\AdBundle\Entity
 */
class Banner extends BaseBanner
{

    /**
     * @var string
     */
    protected $url;

    /**
     * @return int
     */
    public function getType() {
        return self::TYPE_URL;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return Banner
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }
}
