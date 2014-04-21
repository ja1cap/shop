<?php

namespace Weasty\GeonamesBundle\Entity;

use JJs\Bundle\GeonamesBundle\Model\CountryInterface;
use Weasty\DoctrineBundle\Entity\AbstractEntity;
use Weasty\GeonamesBundle\Data\TranslatableGeonameInterface;

/**
 * Class Country
 * @package Weasty\GeonamesBundle\Entity
 */
class Country extends AbstractEntity implements CountryInterface, TranslatableGeonameInterface
{

    /**
     * Unique identifier which represents the country in the local database.
     *
     * @var integer
     */
    protected $id;

    /**
     * ISO code (2 character)
     *
     * @var string
     */
    protected $code;

    /**
     * Name
     *
     * @var string
     */
    protected $name;

    /**
     * Top level domain
     *
     * @var string
     */
    protected $domain;

    /**
     * Postal code format
     *
     * @var string
     */
    protected $postalCodeFormat;

    /**
     * Postal code regular expression
     *
     * @var string
     */
    protected $postalCodeRegex;

    /**
     * Phone number prefix
     *
     * Where there is more than one possible phone prefix the different prefixes
     * will be separated by commas.
     *
     * @var string
     */
    protected $phonePrefix;

    /**
     * @var array
     *
     */
    protected $localeNames;

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
     * Returns the unique identifier of this country in the local database
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
     * Gets the unique 2 character ISO code of this country
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Sets the unique 2 character ISO code of this country
     *
     * @param string $code Country code
     * @return Country
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Gets the name by which this country is internationally recognised
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the name by which this country is internationally recognised
     *
     * @param string $name Country name
     * @return Country
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Gets the top level domain suffix of the country
     *
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Sets the top level domain suffix of the country
     *
     * @param string $domain Domain
     * @return $this
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * Gets the format which postal codes from the country are expected to
     * adhere to
     *
     * @return string
     */
    public function getPostalCodeFormat()
    {
        return $this->postalCodeFormat;
    }

    /**
     * Sets the format which postal codes from the country are expected to
     * adhere to
     *
     * @param string $postalCodeFormat
     * @return Country
     */
    public function setPostalCodeFormat($postalCodeFormat)
    {
        $this->postalCodeFormat = $postalCodeFormat;

        return $this;
    }

    /**
     * Gets the regular expression which postal codes from the country are
     * expected to match
     *
     * @return string
     */
    public function getPostalCodeRegex()
    {
        return $this->postalCodeRegex;
    }

    /**
     * Sets the regular expression which postal codes from the country are
     * expected to match
     *
     * @param string $postalCodeRegex
     * @return Country
     */
    public function setPostalCodeRegex($postalCodeRegex)
    {
        $this->postalCodeRegex = $postalCodeRegex;

        return $this;
    }

    /**
     * Gets the prefix which is prepened to phone nubmers inside this country
     *
     * @return string
     */
    public function getPhonePrefix()
    {
        return $this->phonePrefix;
    }

    /**
     * Sets the prefix which is prepened to phone nubmers inside this country
     *
     * @param string $phonePrefix
     * @return Country
     */
    public function setPhonePrefix($phonePrefix)
    {
        $this->phonePrefix = $phonePrefix;

        return $this;
    }

    /**
     * Set localeNames
     *
     * @param array $localeNames
     * @return Country
     */
    public function setLocaleNames($localeNames)
    {
        $this->localeNames = $localeNames;

        return $this;
    }

    /**
     * Get localeNames
     *
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
        if(isset($this->localeNames[$locale])){
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
