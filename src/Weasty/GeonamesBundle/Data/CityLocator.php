<?php
namespace Weasty\GeonamesBundle\Data;

use Symfony\Component\HttpFoundation\RequestStack;
use Weasty\GeonamesBundle\Entity\City;

/**
 * Class CityLocator
 * @package Weasty\GeonamesBundle\Data
 */
class CityLocator {

    const COOKIE_NAME = 'weasty_geonames_city';

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * @var \Weasty\GeonamesBundle\Entity\CityRepository
     */
    protected $cityRepository;

    /**
     * @var array
     */
    protected $citiesStorage = array();

    function __construct($locale, $cityRepository)
    {
        $this->locale = $locale;
        $this->cityRepository = $cityRepository;
    }

    /**
     * @return array|mixed
     * @throws \InvalidArgumentException
     */
    public function getCityData(){

        $cityData = array();
        $cityCookie = $this->getRequest() ? $this->getRequest()->cookies->get(self::COOKIE_NAME) : null;

        if($cityCookie){

            $cityData = json_decode($cityCookie);

        }

        return $cityData;

    }

    /**
     * @return null|City
     */
    public function getCity(){

        $cityData = $this->getCityData();
        $cityId = isset($cityData['id']) ? $cityData['id'] : null;
        $city = null;

        if($cityId){

            if(isset($this->citiesStorage[$cityId])){
                return $this->citiesStorage[$cityId];
            }

            $city = $this->getCityRepository()->findOneBy(array(
                'id' => $cityId,
            ));

        }

        if($city instanceof City){

            $this->citiesStorage[$cityId] = $city;

        } else {

            $city = $this->getCityRepository()->getCountryCapitalCity();

        }

        return $city;

    }

    /***
     * @param $latitude
     * @param $longitude
     * @return null|City
     */
    public function locateCity($latitude, $longitude){

        $city = null;

        $cityRepository = $this->getCityRepository();

        if($latitude && $longitude){

            $city = $cityRepository->locateCity($latitude, $longitude);

        }

        if(!$city instanceof City){

            $city = $cityRepository->getCountryCapitalCity();

        }

        return $city;

    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @return \Weasty\GeonamesBundle\Entity\CityRepository
     */
    public function getCityRepository()
    {
        return $this->cityRepository;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param RequestStack $request_stack
     */
    public function setRequest(RequestStack $request_stack)
    {
        $this->request = $request_stack->getCurrentRequest();
    }

} 