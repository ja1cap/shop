<?php
namespace Shop\CatalogBundle\Mapper;

use Doctrine\Common\Util\Inflector;
use Shop\CatalogBundle\Entity\ShippingMethod;
use Shop\CatalogBundle\Entity\ShippingMethodCountry;

/**
 * Class ShippingMethodMapper
 * @package Shop\CatalogBundle\Mapper
 */
class ShippingMethodMapper {

    /**
     * @var ShippingMethod
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
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     * @param $shippingMethod
     */
    function __construct($container, ShippingMethod $shippingMethod)
    {
        $this->countryCode = $container->getParameter('country_code');
        $this->countryRepository = $container->get('weasty.geonames.country.repository');
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
     * @param $cityGeonameIds
     * @return $this
     * @throws \Exception
     */
    public function setCityGeonameIds($cityGeonameIds){
        $this->getShippingMethodCountry()->setCityGeonameIds($cityGeonameIds);
        return $this;
    }

    /**
     * @return array
     */
    public function getCityGeonameIds(){
        return $this->getShippingMethodCountry()->getCityGeonameIds();
    }

    /**
     * @return \Shop\CatalogBundle\Entity\ShippingMethod
     */
    public function getShippingMethod()
    {
        return $this->shippingMethod;
    }

    /**
     * @param $countryCode
     * @return ShippingMethodCountry
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