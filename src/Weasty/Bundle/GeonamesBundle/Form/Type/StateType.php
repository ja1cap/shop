<?php
namespace Weasty\Bundle\GeonamesBundle\Form\Type;

use Symfony\Component\Intl\Locale\Locale;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Weasty\Bundle\DoctrineBundle\Form\Type\EntityType;
use Weasty\Bundle\GeonamesBundle\Entity\StateRepository;

/**
 * Class StateType
 * @package Weasty\Bundle\GeonamesBundle\Form\Type
 */
class StateType extends EntityType {

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

                $queryBuilder = function(StateRepository $repository) use ($countryCode, $type) {

                    $country = $repository->getCountryRepository()->getCountry($countryCode);
                    $states = $repository->findBy(array(
                        'countryId' => $country->getID(),
                    ));

                    $repository->sortByLocale($states, $type->getLocale());

                    return $states;

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
        return 'weasty_geonames_state';
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