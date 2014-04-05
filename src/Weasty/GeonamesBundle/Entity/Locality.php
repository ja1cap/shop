<?php

namespace Weasty\GeonamesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;
use JJs\Bundle\GeonamesBundle\Entity\Timezone;

/**
 * Geographical Locality
 *
 * Identifies a geographical location ranging from large areas to buildings.
 *
 * @ORM\MappedSuperclass()
 * @author Josiah <Josiah@jjs.id.au>
 */
abstract class Locality
{
    /**
     * Locality Identifier
     *
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @ORM\Id
     * @var integer
     */
    protected $id;

    /**
     * GeoNames.org ID
     *
     * Uniquely identifies this locality for syncronization from data on
     * GeoNames.org.
     * 
     * @ORM\Column(name="geoname_id", type="integer", nullable=true)
     * @var integer
     */
    protected $geonameIdentifier;

    /**
     * Country
     *
     * @ORM\ManyToOne(targetEntity="Country")
     * @ORM\JoinColumn(nullable=false)
     * @var Country
     */
    protected $country;

    /**
     * Name (UTF-8 encoded)
     *
     * @ORM\Column(name="name_utf8", type="string")
     * @var string
     */
    protected $nameUtf8;

    /**
     * Name (ASCII encoded)
     *
     * @ORM\Column(name="name_ascii", type="string")
     * @var string
     */
    protected $nameAscii;

    /**
     * Latitude coordinate
     *
     * @ORM\Column(name="latitude", type="float", scale=6, precision=9)
     * @var float
     */
    protected $latitude;

    /**
     * Longitude coordinate
     *
     * @ORM\Column(name="longitude", type="float", scale=6, precision=9)
     * @var float
     */
    protected $longitude;

    /**
     * Timezone
     *
     * @ORM\ManyToOne(targetEntity="JJs\Bundle\GeonamesBundle\Entity\Timezone")
     * @var \JJs\Bundle\GeonamesBundle\Entity\Timezone
     */
    protected $timezone;

    /**
     * Creation date
     *
     * @ORM\Column(name="creation_date", type="datetime")
     * @var DateTime
     */
    protected $creationDate;

    /**
     * Modification date
     *
     * @ORM\Column(name="modification_date", type="datetime", nullable=true)
     * @var DateTime
     */
    protected $modificationDate;

    /**
     * Creates a new locality
     */
    public function __construct()
    {
        $this->creationDate = new DateTime();
    }

    /**
     * Returns the locality ID
     * 
     * @return integer
     */
    public function getID()
    {
        return $this->id;
    }

    /**
     * Returns the GeoNames.org identifier of this locality
     *
     * @return integer
     */
    public function getGeonameIdentifier()
    {
        return $this->geonameIdentifier;
    }

    /**
     * Sets the GeoNames.org identifier of this locality
     * 
     * @param integer $geonameIdentifier Identifier
     *
     * @return Locality
     */
    public function setGeonameIdentifier($geonameIdentifier)
    {
        $this->geonameIdentifier = $geonameIdentifier;

        return $this;
    }

    /**
     * Returns the country
     * 
     * @return Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Sets the country
     * 
     * @param Country $country Country
     * 
     * @return Locality
     */
    public function setCountry(Country $country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Returns the name of the locality
     * 
     * @return string
     */
    public function getName()
    {
        return $this->getNameUtf8() ?: $this->getNameAscii();
    }

    /**
     * Returns the UTF8 encoded name of the locality
     * 
     * @return string
     */
    public function getNameUtf8()
    {
        return $this->nameUtf8;
    }

    /**
     * Sets the UTF8 encoded name of the locality
     * 
     * @param string $name Locality name
     *
     * @return Locality
     */
    public function setNameUtf8($name)
    {
        $this->nameUtf8 = $name;

        return $this;
    }

    /**
     * Returns the ASCII encoded name of the locality
     * 
     * @return string
     */
    public function getNameAscii()
    {
        return $this->nameAscii;
    }

    /**
     * Sets the ASCII encoded name of the locality
     * 
     * @param string $name Name
     *
     * @return Locality
     */
    public function setNameAscii($name)
    {
        $this->nameAscii = $name;

        return $this;
    }

    /**
     * Returns the approximate latitude of the locality
     * 
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Sets the latitude of the locality
     * 
     * @param string $latitude Latitude
     *
     * @return float
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Returns the longitude of the locality
     * 
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Sets the longitude of the locality
     * 
     * @param float $longitude Longitude
     *
     * @return Locality
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Returns the timezone
     * 
     * @return Timezone
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * Sets the timezone
     * 
     * @param Timezone $timezone Timezone
     *
     * @return Locality
     */
    public function setTimezone(Timezone $timezone = null)
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * Returns the creation date of this locality
     * 
     * @return DateTime
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * Returns the modification date of this locality
     * 
     * @return DateTime
     */
    public function getModificationDate()
    {
        return $this->modificationDate;
    }

    /**
     * Sets the modification date of this locality
     * 
     * @param DateTime $modificationDate Modification date
     * 
     * @return Locality
     */
    public function setModificationDate(DateTime $modificationDate)
    {
        $this->modificationDate = $modificationDate;

        return $this;
    }
}
