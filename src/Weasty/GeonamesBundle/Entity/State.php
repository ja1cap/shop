<?php

namespace Weasty\GeonamesBundle\Entity;

use DateTime;
use JJs\Bundle\GeonamesBundle\Entity\Timezone;
use Doctrine\ORM\Mapping as ORM;
use Weasty\GeonamesBundle\Data\TranslatableGeonameInterface;

/**
 * Class State
 * @package Weasty\GeonamesBundle\Entity
 */
class State extends Locality implements TranslatableGeonameInterface
{

    /**
     * Locale names
     * @var array
     */
    protected $localeNames;

    /**
     * @var string
     */
    protected $adminCode;

    /**
     * Locality Identifier
     *
     * @var integer
     */
    protected $id;

    /**
     * GeoNames.org ID
     *
     * Uniquely identifies this locality for syncronization from data on
     * GeoNames.org.
     *
     * @var integer
     */
    protected $geonameIdentifier;

    /**
     * Country id
     *
     * @var string
     */
    protected $countryId;

    /**
     * Country
     *
     * @var Country
     */
    protected $country;

    /**
     * Name (UTF-8 encoded)
     *
     * @var string
     */
    protected $nameUtf8;

    /**
     * Name (ASCII encoded)
     *
     * @var string
     */
    protected $nameAscii;

    /**
     * Latitude coordinate
     *
     * @var float
     */
    protected $latitude;

    /**
     * Longitude coordinate
     *
     * @var float
     */
    protected $longitude;

    /**
     * Timezone
     *
     * @var \JJs\Bundle\GeonamesBundle\Entity\Timezone
     */
    protected $timezone;

    /**
     * Creation date
     *
     * @var DateTime
     */
    protected $creationDate;

    /**
     * Modification date
     *
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
        $this->countryId = $country ? $country->getID() : null;
        return $this;
    }

    /**
     * Returns the name of the locality
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

    /**
     * @param string $countryId
     */
    public function setCountryId($countryId)
    {
        $this->countryId = $countryId;
    }

    /**
     * @return string
     */
    public function getCountryId()
    {
        return $this->countryId;
    }

    /**
     * @param string $adminCode
     */
    public function setAdminCode($adminCode)
    {
        $this->adminCode = $adminCode;
    }

    /**
     * @return string
     */
    public function getAdminCode()
    {
        return $this->adminCode;
    }

    /**
     * @param array $localeNames
     * @return $this
     */
    public function setLocaleNames($localeNames)
    {
        $this->localeNames = $localeNames;
        return $this;
    }

    /**
     * @return array
     */
    public function getLocaleNames()
    {
        return $this->localeNames;
    }

    /**
     * @param $locale
     * @param $name
     * @return $this
     */
    public function addLocaleName($locale, $name){
        $this->localeNames[$locale] = $name;
        return $this;
    }

    /**
     * @param $locale
     * @return string
     */
    public function getLocaleName($locale){
        if($locale && isset($this->localeNames[$locale])){
            return $this->localeNames[$locale];
        }
        return $this->getName();
    }

    /**
     * @param $locale
     * @return $this
     */
    public function removeLocaleName($locale){
        if(isset($this->localeNames[$locale])){
            unset($this->localeNames[$locale]);
        }
        return $this;
    }

}
