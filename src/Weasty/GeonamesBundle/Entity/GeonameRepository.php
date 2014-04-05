<?php
namespace Weasty\GeonamesBundle\Entity;
use Weasty\DoctrineBundle\Entity\AbstractRepository;
use Weasty\GeonamesBundle\Resources\TranslatableGeoname;

/**
 * Class GeonameRepository
 * @package Weasty\GeonamesBundle\Entity
 */
abstract class GeonameRepository extends AbstractRepository {

    /**
     * @var \Weasty\GeonamesBundle\Data\GeonameLoader
     */
    protected $geonameLoader;

    /**
     * @param \Weasty\GeonamesBundle\Data\GeonameLoader $localityLoader
     * @return $this
     */
    public function setGeonameLoader($localityLoader)
    {
        $this->geonameLoader = $localityLoader;
        return $this;
    }

    /**
     * @return \Weasty\GeonamesBundle\Data\GeonameLoader
     */
    public function getGeonameLoader()
    {
        return $this->geonameLoader;
    }

    /**
     * @param $geonames
     * @param $locale
     * @return void
     */
    public function sortByLocale(&$geonames, $locale){

        $geonamesIndexedByTranslatedName = array();

        foreach($geonames as $locality){
            if($locality instanceof TranslatableGeoname){
                $geonamesIndexedByTranslatedName[$locality->getLocaleName($locale)] = $locality;
            }
        }

        ksort($geonamesIndexedByTranslatedName);

        $geonames = array_values($geonamesIndexedByTranslatedName);

    }

} 