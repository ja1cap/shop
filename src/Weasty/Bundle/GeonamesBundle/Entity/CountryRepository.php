<?php

namespace Weasty\Bundle\GeonamesBundle\Entity;

use JJs\Bundle\GeonamesBundle\Model\CountryInterface;
use JJs\Bundle\GeonamesBundle\Model\CountryRepositoryInterface;

/***
 * Class CountryRepository
 * @package Weasty\Bundle\GeonamesBundle\Entity
 */
class CountryRepository extends GeonameRepository implements CountryRepositoryInterface
{

    /**
     * Returns the country which matches the specified country code
     *
     * If no country matches the specified code, returns null.
     *
     * When a CountryInterface instance is passed to this method the code will
     * be extracted from the country.
     *
     * @param string|CountryInterface $identifier Country code
     *
     * @return Country
     */
    public function getCountry($identifier)
    {

        // Pass through country instances
        if ($identifier instanceof Country) return $identifier;

        // Extract the code from country interfaces as required
        if ($identifier instanceof CountryInterface) $identifier = $identifier->getCode();

        // Find the country by its code
        return $this->findOneBy(['code' => $identifier]);

    }

    /**
     * Returns all countries
     *
     * @return CountryInterface[]
     */
    public function getAllCountries()
    {
        return $this->findAll();
    }

    /**
     * Updates the country in this repository
     *
     * @param CountryInterface $data Country
     *
     * @return CountryInterface
     */
    public function saveCountry(CountryInterface $data)
    {

        if ($data instanceof Country) {
            $country = $data;
        } else {
            $country = $this->getCountry($data) ?: new Country();
            $this->copyCountry($data, $country);
        }

        // Persist and flush the entity in the entity manager
        $em = $this->getEntityManager();
        $em->persist($country);
        $em->flush();

    }

    /**
     * Copies the data from the country to the destination
     *
     * @param \IteratorAggregate|array|CountryInterface $source      Source
     * @param Country          $country Destination
     */
    public function copyCountry($source, Country $country)
    {

        if($source instanceof \IteratorAggregate){

            foreach($source as $propertyName => $value){
                $country[$propertyName] = $value;
            }

        } elseif($source instanceof CountryInterface) {

            $code             = $source->getCode();
            $name             = $source->getName();
            $domain           = $source->getDomain();
            $postalCodeFormat = $source->getPostalCodeFormat();
            $postalCodeRegex  = $source->getPostalCodeRegex();
            $phonePrefix      = $source->getPhonePrefix();

            // Copy the country code
            if ($code !== $country->getCode()) {
                $country->setCode($code);
            }

            // Copy the country name
            if ($name !== $country->getName()) {
                $country->setName($name);
            }

            // Copy the top level domain suffix
            if ($domain !== $country->getDomain()) {
                $country->setDomain($domain);
            }

            // Copy the postal code format
            if ($postalCodeFormat !== $country->getPostalCodeFormat()) {
                $country->setPostalCodeFormat($postalCodeFormat);
            }

            // Copy the postal code regex
            if ($postalCodeRegex !== $country->getPostalCodeRegex()) {
                $country->setPostalCodeRegex($postalCodeRegex);
            }

            // Copy the phone prefix
            if ($phonePrefix !== $country->getPhonePrefix()) {
                $country->setPhonePrefix($phonePrefix);
            }

        }

        $geonameId = $country->getGeonameIdentifier();
        $geonameData = $this->getGeonameLoader()->load($geonameId);

        $localeNames = array();
        $locales = array(
            'en',
            'ru',
        );

        if($geonameData && isset($geonameData['alternateNames']) && is_array($geonameData['alternateNames'])){

            foreach($geonameData['alternateNames'] as $alternateName){

                if(isset($alternateName['lang']) && in_array($alternateName['lang'], $locales)){
                    $localeNames[$alternateName['lang']] = $alternateName['name'];
                }

            }

        }

        if($localeNames){
            $country->setLocaleNames($localeNames);
        }

    }

}
