<?php

namespace Shop\MainBundle\Entity;

use Application\Sonata\MediaBundle\Entity\Media;
use Doctrine\ORM\Mapping as ORM;
use Weasty\DoctrineBundle\Entity\AbstractEntity;

/**
 * Class Phone
 * @package Shop\MainBundle\Entity
 */
class Phone extends AbstractEntity
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $countryCode;

    /**
     * @var integer
     */
    private $code;

    /**
     * @var string
     */
    private $number;

    /**
     * @var integer
     */
    private $iconId;

    /**
     * @var \Application\Sonata\MediaBundle\Entity\Media
     */
    private $icon;

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
     * Set countryCode
     *
     * @param integer $countryCode
     * @return Phone
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    /**
     * Get countryCode
     *
     * @return integer 
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * Set code
     *
     * @param integer $code
     * @return Phone
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return integer 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set number
     *
     * @param string $number
     * @return Phone
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return string 
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set iconId
     *
     * @param integer $iconId
     * @return Phone
     */
    public function setIconId($iconId)
    {
        $this->iconId = $iconId;

        return $this;
    }

    /**
     * Get iconId
     *
     * @return integer 
     */
    public function getIconId()
    {
        return $this->iconId;
    }

    /**
     * Set icon
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media $icon
     * @return Phone
     */
    public function setIcon(Media $icon = null)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Get icon
     *
     * @return \Application\Sonata\MediaBundle\Entity\Media 
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @return string
     */
    function __toString()
    {

        $phone = '';

        if($this->getCountryCode()){
            $phone .= '+' . $this->getCountryCode() . ' ';
        }

        if($this->getCode()){
            $phone .= '(' . $this->getCode() . ') ';
        }

        $phone .= $this->getNumber();

        return $phone;

    }

}
