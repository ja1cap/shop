<?php
namespace Shop\CatalogBundle\Form\Type;

use Shop\CatalogBundle\Entity\ShippingMethod;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Weasty\GeonamesBundle\Entity\CountryRepository;
use Weasty\GeonamesBundle\Entity\CityRepository;

/**
 * Class ManufacturerType
 * @package Shop\CatalogBundle\Form\Type
 */
class ShippingMethodType extends AbstractType {

    /**
     * @var string
     */
    protected $countryCode;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var CountryRepository
     */
    protected $countryRepository;

    /**
     * @var CityRepository
     */
    protected $cityRepository;

    /**
     * @param $countryRepository
     * @param $cityRepository
     * @param $countryCode
     * @param $locale
     */
    function __construct($countryRepository, $cityRepository, $countryCode, $locale)
    {
        $this->countryRepository = $countryRepository;
        $this->cityRepository = $cityRepository;
        $this->countryCode = $countryCode;
        $this->locale = $locale;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $countryRepository = $this->getCountryRepository();

//        $countries = $countryRepository->getAllCountries();
//        $countryRepository->sortByLocale($countries, $this->getLocale());
//
//        $countriesChoices = array();
//
//        /**
//         * @var $country \Weasty\GeonamesBundle\Entity\Country
//         */
//        foreach($countries as $country){
//            $countriesChoices[$country->getCode()] = $country->getLocaleName($this->getLocale());
//        }

        $country = $countryRepository->getCountry($this->getCountryCode());
        $cityRepository = $this->getCityRepository();

        $cities = $cityRepository->getCountryCities($country);
        //$cityRepository->sortByLocale($cities, $this->getLocale());

        $citiesChoices = array();

        /**
         * @var $city \Weasty\GeonamesBundle\Entity\City
         */
        foreach($cities as $city){
            $citiesChoices[$city->getGeonameIdentifier()] = $city->getLocaleName($this->getLocale());
        }

        $builder
            ->add('name', 'text', array(
                'required' => true,
                'label' => 'Название',
            ))
            ->add('status', 'choice', array(
                'required' => true,
                'choices' => ShippingMethod::$statuses,
                'label' => 'Статус',
            ))
            ->add('description', 'textarea', array(
                'required' => false,
                'label' => 'Описание',
            ))
//            ->add('countries', 'country', array(
//                'choices' => $countriesChoices,
//                'multiple' => true,
//                'required' => true,
//                'label' => 'Страны',
//                'mapped' => false
//            ))
            ->add('cityGeonameIds', 'choice', array(
                'choices' => $citiesChoices,
                'multiple' => true,
                'required' => true,
                'label' => 'Города',
            ))
        ;


        $builder
            ->add('save', 'submit', array(
                'label' => 'Сохранить',
            ));

    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'shipping_method';
    }

    /**
     * @return \Weasty\GeonamesBundle\Entity\CountryRepository
     */
    public function getCountryRepository()
    {
        return $this->countryRepository;
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
     * @return string
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

}
