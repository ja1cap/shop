<?php
namespace Weasty\GeonamesBundle\Twig;

/**
 * Class CityExtension
 * @package Weasty\GeonamesBundle\Twig
 */
class CityExtension extends \Twig_Extension {

    /**
     * @var \Weasty\GeonamesBundle\Entity\CityRepository
     */
    protected $cityRepository;

    /**
     * @param \Weasty\GeonamesBundle\Entity\CityRepository $cityRepository
     */
    function __construct($cityRepository)
    {
        $this->cityRepository = $cityRepository;
    }

    /**
     * @return array
     */
    public function getFunctions(){
        return array(
            new \Twig_SimpleFunction('weasty_geonames_city', array($this, 'getCity')),
            new \Twig_SimpleFunction('weasty_geonames_cities', array($this, 'getCities')),
        );
    }

    /**
     * @param $geonameIdentifier
     * @return null|\Weasty\GeonamesBundle\Entity\City
     */
    public function getCity($geonameIdentifier){
        return $geonameIdentifier ? $this->getCityRepository()->findOneBy(array(
            'geonameIdentifier' => $geonameIdentifier
        )) : null;
    }

    /**
     * @param $geonameIdentifiers
     * @return array
     */
    public function getCities($geonameIdentifiers){
        return $geonameIdentifiers ? $this->getCityRepository()->findBy(array(
            'geonameIdentifier' => $geonameIdentifiers
        )) : null;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'weasty_geonames_cities';
    }

    /**
     * @return \Weasty\GeonamesBundle\Entity\CityRepository
     */
    public function getCityRepository()
    {
        return $this->cityRepository;
    }

} 