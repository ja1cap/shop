<?php
namespace Weasty\GeonamesBundle\Twig;

/**
 * Class CountriesExtension
 * @package Weasty\GeonamesBundle\Twig
 */
class CountriesExtension extends \Twig_Extension {

    /**
     * @var \Weasty\GeonamesBundle\Entity\CountryRepository
     */
    protected $countryRepository;

    /**
     * @param \Weasty\GeonamesBundle\Entity\CountryRepository $countryRepository
     */
    function __construct($countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    /**
     * @return array
     */
    public function getFunctions(){
        return array(
            new \Twig_SimpleFunction('weasty_geonames_country', array($this, 'getCountry')),
            new \Twig_SimpleFunction('weasty_geonames_countries', array($this, 'getCountries')),
        );
    }

    /**
     * @param $code
     * @return null|\Weasty\GeonamesBundle\Entity\Country
     */
    public function getCountry($code){
        return $code ? $this->getCountryRepository()->findOneBy(array(
            'code' => $code
        )) : null;
    }

    /**
     * @param $codes
     * @return array
     */
    public function getCities($codes){
        return $codes ? $this->getCountryRepository()->findBy(array(
            'code' => $codes
        )) : null;
    }


    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'weasty_geonames_countries';
    }

    /**
     * @return \Weasty\GeonamesBundle\Entity\CountryRepository
     */
    public function getCountryRepository()
    {
        return $this->countryRepository;
    }

} 