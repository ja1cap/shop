<?php
namespace Shop\ShippingBundle\Mapper;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Util\Inflector;
use Shop\ShippingBundle\Entity\ShippingMethod;
use Shop\ShippingBundle\Entity\ShippingMethodCountry;
use Weasty\GeonamesBundle\Entity\City;
use Weasty\GeonamesBundle\Entity\State;

/**
 * Class ShippingMethodMapper
 * @package Shop\ShippingBundle\Mapper
 */
class ShippingMethodMapper {

    /**
     * @var \Shop\ShippingBundle\Entity\ShippingMethod
     */
    protected $shippingMethod;

    /**
     * @var string
     */
    protected $countryCode;

    /**
     * @var \Weasty\GeonamesBundle\Entity\CountryRepository
     */
    protected $countryRepository;

    /**
     * @var \Weasty\GeonamesBundle\Entity\StateRepository
     */
    protected $stateRepository;

    /**
     * @var \Weasty\GeonamesBundle\Entity\CityRepository
     */
    protected $cityRepository;

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     * @param $shippingMethod
     */
    function __construct($container, ShippingMethod $shippingMethod)
    {
        $this->countryCode = $container->getParameter('country_code');
        $this->countryRepository = $container->get('weasty_geonames.country.repository');
        $this->stateRepository = $container->get('weasty_geonames.state.repository');
        $this->cityRepository = $container->get('weasty_geonames.city.repository');
        $this->shippingMethod = $shippingMethod;
    }

    /**
     * @return string
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * @param $countryCode
     * @return $this
     */
    public function setCountryCode($countryCode){
        $this->getShippingMethodCountry($countryCode);
        return $this;
    }

    /**
     * @param ArrayCollection $cities
     * @return $this
     * @throws \Exception
     */
    public function setCities($cities){

        $geonameIdentifiers = $cities->map(function(City $city){
            return $city->getGeonameIdentifier();
        })->toArray();

        $this->getShippingMethodCountry()->setCityGeonameIds($geonameIdentifiers);

        return $this;

    }

    /**
     * @return ArrayCollection|null
     */
    public function getCities(){

        $cities = null;
        $geonameIdentifiers = $this->getShippingMethodCountry()->getCityGeonameIds();

        if($geonameIdentifiers){

            $cities = new ArrayCollection($this->getCityRepository()->findBy(array(
                'geonameIdentifier' => $geonameIdentifiers
            )));

        }

        return $cities;

    }

    /**
     * @param ArrayCollection $states
     * @return $this
     * @throws \Exception
     */
    public function setStates($states){

        $geonameIdentifiers = $states->map(function(State $state){
            return $state->getGeonameIdentifier();
        })->toArray();

        $this->getShippingMethodCountry()->setStateGeonameIds($geonameIdentifiers);

        return $this;

    }

    /**
     * @return ArrayCollection|null
     */
    public function getStates(){

        $states = null;
        $geonameIdentifiers = $this->getShippingMethodCountry()->getStateGeonameIds();

        if($geonameIdentifiers){

            $states = new ArrayCollection($this->getStateRepository()->findBy(array(
                'geonameIdentifier' => $geonameIdentifiers
            )));

        }

        return $states;

    }

    /**
     * @return \Shop\ShippingBundle\Entity\ShippingMethod
     */
    public function getShippingMethod()
    {
        return $this->shippingMethod;
    }

    /**
     * @param $countryCode
     * @return \Shop\ShippingBundle\Entity\ShippingMethodCountry
     * @throws \Exception
     */
    protected function getShippingMethodCountry($countryCode = null){

        $countryCode = $countryCode ?: $this->getCountryCode();

        $shippingMethodCountry = $this->getShippingMethod()->getCountries()->filter(function(ShippingMethodCountry $country) use ($countryCode) {
            return $country->getCountryCode() == $countryCode;
        })->current();

        if(!$shippingMethodCountry instanceof ShippingMethodCountry){

            $country = $this->getCountryRepository()->getCountry($countryCode);

            if($country){

                $shippingMethodCountry = new ShippingMethodCountry();
                $shippingMethodCountry->setCountryCode($country->getCode());

                $this->getShippingMethod()->addCountry($shippingMethodCountry);

            } else {

                throw new \Exception('Country not found ' . $countryCode, 404);

            }

        }

        return $shippingMethodCountry;

    }

    /**
     * @return \Weasty\GeonamesBundle\Entity\CountryRepository
     */
    public function getCountryRepository()
    {
        return $this->countryRepository;
    }

    /**
     * @return \Weasty\GeonamesBundle\Entity\CityRepository
     */
    public function getCityRepository()
    {
        return $this->cityRepository;
    }

    /**
     * @return \Weasty\GeonamesBundle\Entity\StateRepository
     */
    public function getStateRepository()
    {
        return $this->stateRepository;
    }

    /**
     * @param $name
     * @return mixed
     */
    function __get($name)
    {
        $method = 'get' . Inflector::classify($name);
        if(method_exists($this, $method)){
            return $this->$method();
        }
        return $this->getShippingMethod()->offsetGet($name);
    }

    /**
     * @param $name
     * @param $value
     */
    function __set($name, $value)
    {
        $method = 'set' . Inflector::classify($name);
        if(method_exists($this, $method)){
            $this->$method($value);
        }
        $this->getShippingMethod()->offsetSet($name, $value);
    }

}