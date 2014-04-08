<?php

namespace Weasty\GeonamesBundle\Entity;

use DateTime;
use JJs\Bundle\GeonamesBundle\Entity\Timezone;
use Weasty\DoctrineBundle\Entity\AbstractEntity;

/**
 * Geographical Locality
 *
 * Identifies a geographical location ranging from large areas to buildings.
 *
 * */
abstract class Locality extends AbstractEntity
{
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
    abstract public function getID();

    /**
     * Returns the GeoNames.org identifier of this locality
     *
     * @return integer
     */
    abstract public function getGeonameIdentifier();

    /**
     * Sets the GeoNames.org identifier of this locality
     * 
     * @param integer $geonameIdentifier Identifier
     *
     * @return Locality
     */
    abstract public function setGeonameIdentifier($geonameIdentifier);

    /**
     * Returns the country
     * 
     * @return Country
     */
    abstract public function getCountry();

    /**
     * Sets the country
     * 
     * @param Country $country Country
     * 
     * @return Locality
     */
    abstract public function setCountry(Country $country);

    /**
     * Returns the name of the locality
     * @return string
     */
    abstract public function getName();

    /**
     * Returns the UTF8 encoded name of the locality
     * 
     * @return string
     */
    abstract public function getNameUtf8();

    /**
     * Sets the UTF8 encoded name of the locality
     * 
     * @param string $name Locality name
     *
     * @return Locality
     */
    abstract public function setNameUtf8($name);

    /**
     * Returns the ASCII encoded name of the locality
     * 
     * @return string
     */
    abstract public function getNameAscii();

    /**
     * Sets the ASCII encoded name of the locality
     * 
     * @param string $name Name
     *
     * @return Locality
     */
    abstract public function setNameAscii($name);

    /**
     * Returns the approximate latitude of the locality
     * 
     * @return float
     */
    abstract public function getLatitude();

    /**
     * Sets the latitude of the locality
     * 
     * @param string $latitude Latitude
     *
     * @return float
     */
    abstract public function setLatitude($latitude);

    /**
     * Returns the longitude of the locality
     * 
     * @return float
     */
    abstract public function getLongitude();

    /**
     * Sets the longitude of the locality
     * 
     * @param float $longitude Longitude
     *
     * @return Locality
     */
    abstract public function setLongitude($longitude);

    /**
     * Returns the timezone
     * 
     * @return Timezone
     */
    abstract public function getTimezone();

    /**
     * Sets the timezone
     * 
     * @param Timezone $timezone Timezone
     *
     * @return Locality
     */
    abstract public function setTimezone(Timezone $timezone = null);

    /**
     * Returns the creation date of this locality
     * 
     * @return DateTime
     */
    abstract public function getCreationDate();

    /**
     * Returns the modification date of this locality
     * 
     * @return DateTime
     */
    abstract public function getModificationDate();

    /**
     * Sets the modification date of this locality
     * 
     * @param DateTime $modificationDate Modification date
     * 
     * @return Locality
     */
    abstract public function setModificationDate(DateTime $modificationDate);

    /**
     * @param string $countryId
     */
    abstract public function setCountryId($countryId);

    /**
     * @return string
     */
    abstract public function getCountryId();

}
