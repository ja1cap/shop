<?php
namespace Weasty\GeonamesBundle\Entity;

use JJs\Bundle\GeonamesBundle\Import\Locality as ImportLocality;
use JJs\Bundle\GeonamesBundle\Model\LocalityInterface;

/**
 * Class CityRepository
 * @package Weasty\GeonamesBundle\Entity
 */
class CityRepository extends LocalityRepository
{

    /**
     * @param $country
     * @return array
     */
    public function getCountryCities($country){

        $country = $this->getCountryRepository()->getCountry($country);

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select(array(
                'c.*'
            ))
            ->from('WeastyGeonamesBundle:City', 'c')
            ->andWhere($qb->expr()->andX(
                $qb->expr()->eq('c.country_id', $country->getID()),
                $qb->expr()->gte('c.population', 5000)
            ))
            ->addOrderBy('c.population', 'DESC')
        ;

        $this->convertDqlToSql($qb);
        $sql = (string)$qb;

        $rsm = $this->createResultSetMappingFromMetadata('WeastyGeonamesBundle:City', 'c');

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);

        return $query->getResult();

    }

    /**
     * Returns a reference city from the database which matches the specified
     * city
     * 
     * @param mixed $city City
     * 
     * @return City
     */
    public function getCity($city)
    {
        // Pass through existing references
        if ($city instanceof City) return $city;

        // Load cities using their geoname id for locality interfaces
        if ($city instanceof LocalityInterface) {
            return $this->findOneBy(['geonameIdentifier' => $city->getGeonameIdentifier()]);
        }

        // Load the city as if it was the primary key
        return $this->findOneBy(['id' => $city->getID()]);
    }

    /**
     * Imports a locality as a city
     * 
     * @param LocalityInterface $locality Locality
     * 
     * @return City
     */
    public function importLocality(LocalityInterface $locality)
    {
        // No change is necessary for state instances
        if ($locality instanceof City) return $locality;

        // Load the existing state for the locality, or create a new instance
        $city = $this->getCity($locality) ?: new City();

        // Copy data from the import locality into an existing or new state
        // instance
        $this->copyLocality($locality, $city);

        if($locality instanceof ImportLocality){

            $city
                ->setPopulation($locality->getPopulation())
            ;

            if($locality->getPopulation() >= 5000){

                $geonameId = $locality->getGeonameIdentifier();
                $geonameData = $this->getGeonameLoader()->load($geonameId);

                $localeNames = array();
                $locales = array(
                    'en',
                    'ru',
                );

                if($geonameData && isset($geonameData['alternateNames']) && is_array($geonameData['alternateNames'])){

                    foreach($geonameData['alternateNames'] as $alternateName){

                        if(isset($alternateName['lang']) && in_array($alternateName['lang'], $locales)){
                            $localeNames[$alternateName['lang']] = mb_convert_case($alternateName['name'], MB_CASE_TITLE, 'UTF-8');
                        }

                    }

                }

                if($localeNames){

                    $city->setLocaleNames($localeNames);

                } else {

                    //@TODO create translate service
                    $locale = 'ru';
                    $localeName = null;

                    $apiKey = 'trnsl.1.1.20140404T183201Z.aa6f45d19c2da3f1.62b663d01540f2d779aba5152e44bb25f2243aac';
                    $jsonResponse = @file_get_contents("https://translate.yandex.net/api/v1.5/tr.json/translate?key=$apiKey&lang=en-$locale&text=" . urlencode(str_replace('\'', '', $city->getNameAscii())));
                    $response = json_decode($jsonResponse, true);
                    if(isset($response['code']) && $response['code'] == 200){
                        $localeName = current($response['text']);
                    }

                    if($localeName){

                        switch($locale){
                            case 'ru':

                                if(!preg_match('/[А-Яа-яЁё]/u', $localeName)){

                                    $alternativeNames = explode(',', $locality->getAlternateNames());
                                    foreach($alternativeNames as $name){
                                        if(preg_match('/[А-Яа-яЁё]/u', $name)){
                                            $localeName = mb_convert_case($name, MB_CASE_TITLE, 'UTF-8');
                                            break;
                                        }
                                    }

                                }

                                break;
                        }

                        $city->addLocaleName($locale, $localeName);

                    }

                }

            }

        }

        // Return the state instance from the locality
        return $city;

    }

}