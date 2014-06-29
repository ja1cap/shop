<?php
namespace Weasty\Bundle\GeonamesBundle\Entity;
use Weasty\Doctrine\Entity\AbstractRepository;
use Weasty\Bundle\GeonamesBundle\Data\TranslatableGeonameInterface;

/**
 * Class GeonameRepository
 * @package Weasty\Bundle\GeonamesBundle\Entity
 */
abstract class GeonameRepository extends AbstractRepository {

    /**
     * @var \Weasty\Bundle\GeonamesBundle\Data\GeonameLoader
     */
    protected $geonameLoader;

    /**
     * @param \Weasty\Bundle\GeonamesBundle\Data\GeonameLoader $localityLoader
     * @return $this
     */
    public function setGeonameLoader($localityLoader)
    {
        $this->geonameLoader = $localityLoader;
        return $this;
    }

    /**
     * @return \Weasty\Bundle\GeonamesBundle\Data\GeonameLoader
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
            if($locality instanceof TranslatableGeonameInterface){
                $geonamesIndexedByTranslatedName[$locality->getLocaleName($locale)] = $locality;
            }
        }

        ksort($geonamesIndexedByTranslatedName);

        $geonames = array_values($geonamesIndexedByTranslatedName);

    }

} 