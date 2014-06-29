<?php
namespace Weasty\Bundle\GeonamesBundle\Twig;

/**
 * Class CountryExtension
 * @package Weasty\Bundle\GeonamesBundle\Twig
 */
class CountryExtension extends \Twig_Extension {

    /**
     * @var \Weasty\Bundle\GeonamesBundle\Entity\CountryRepository
     */
    protected $countryRepository;

    /**
     * @param \Weasty\Bundle\GeonamesBundle\Entity\CountryRepository $countryRepository
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
     * @return null|\Weasty\Bundle\GeonamesBundle\Entity\Country
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
     * @return \Weasty\Bundle\GeonamesBundle\Entity\CountryRepository
     */
    public function getCountryRepository()
    {
        return $this->countryRepository;
    }

} 