<?php
namespace Weasty\GeonamesBundle\Form\Type;

use Symfony\Component\Intl\Locale\Locale;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Weasty\DoctrineBundle\Form\Type\RepositoryType;
use Weasty\GeonamesBundle\Entity\CityRepository;

/**
 * Class CityType
 * @package Weasty\GeonamesBundle\Form\Type
 */
class CityType extends RepositoryType {

    /**
     * @var string
     */
    protected $countryCode;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {

        parent::setDefaultOptions($resolver);

        $type = $this;
        $loader = function (Options $options) use ($type) {

            $countryCode = $options->has('country_code') ? $options['county_code'] : $type->getCountryCode();
            $queryBuilder = $options['query_builder'];

            if($queryBuilder == null){

                $queryBuilder = function(CityRepository $cityRepository) use ($countryCode) {

                    $country = $cityRepository->getCountryRepository()->getCountry($countryCode);
                    $cities = $cityRepository->getCountryCities($country);
                    return $cities;

                };

            }

            if (null !== $queryBuilder) {
                return $type->getLoader($options['em'], $queryBuilder, $options['class']);
            }

            return null;

        };

        $resolver->setDefaults(array(
            'loader' => $loader,
            'property' => 'localeName(' . $this->getLocale() . ')',
        ));

    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'weasty_geonames_city';
    }

    /**
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale ?: Locale::getDefault();
    }

    /**
     * @param string $countryCode
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;
    }

    /**
     * @return string
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

}