<?php
namespace Weasty\Bundle\GeonamesBundle\Entity;

use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr;
use JJs\Bundle\GeonamesBundle\Import\Locality as ImportLocality;
use JJs\Bundle\GeonamesBundle\Model\LocalityInterface;

/**
 * Class CityRepository
 * @package Weasty\Bundle\GeonamesBundle\Entity
 */
class CityRepository extends LocalityRepository
{

    /**
     * @var string
     */
    protected $defaultCountryCode;

    /**
     * @param $countryCode
     * @return null|City
     */
    public function getCountryCapitalCity($countryCode = null){

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select('c')
            ->from('WeastyGeonamesBundle:Country', 'co')
            ->join('WeastyGeonamesBundle:City', 'c', Expr\Join::WITH, $qb->expr()->andX(
                $qb->expr()->eq('c.nameUtf8', 'co.capital'),
                $qb->expr()->eq('co.id', 'c.countryId')
            ))
            ->andWhere($qb->expr()->eq('co.code', $qb->expr()->literal($countryCode ?: $this->getDefaultCountryCode())))
        ;

        return current($qb->getQuery()->getResult());

    }

    /**
     * @param $latitude
     * @param $longitude
     * @return null|City
     */
    public function locateCity($latitude, $longitude){

        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb
            ->select(array(
                'c.id',
                '(6371 * acos(cos(radians(' . $latitude . ')) * cos(radians(c.latitude)) * cos(radians(c.longitude) - radians(' . $longitude . ')) + sin(radians(' . $latitude . ')) * sin(radians(c.latitude)))) AS distance'
            ))
            ->from('WeastyGeonamesBundle:City', 'c')
            ->andHaving($qb->expr()->lte('distance', 25))
            ->orderBy('distance', 'ASC')
        ;

        $this->convertDqlToSql($qb);
        $sql = (string)$qb;
        $sql .= ' LIMIT 1';

        $id = $this->getEntityManager()->getConnection()->fetchColumn($sql);
        $city = $id ? $this->find($id) : null;

        return $city;


    }

    /**
     * @param $country
     * @return array
     */
    public function getCountryCities($country = null){

        $country = $this->getCountryRepository()->getCountry($country ?: $this->getDefaultCountryCode());

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select(array(
                'c'
            ))
            ->from('WeastyGeonamesBundle:City', 'c')
            ->andWhere($qb->expr()->andX(
                $qb->expr()->eq('c.countryId', $country->getID()),
                $qb->expr()->gte('c.population', 5000)
            ))
            ->addOrderBy('c.population', 'DESC')
        ;

        $query = $qb->getQuery();

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
     * @throws \Exception
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

                $city->setStateAdminCode($locality->getAdmin1Code());

                if($city->getStateAdminCode()){

                    $state = $this->getStateRepository()->findOneBy(array(
                        'countryId' => $city->getCountry()->getID(),
                        'adminCode' => $city->getStateAdminCode(),
                    ));

                    if($state instanceof State){

                        $city->setState($state);

                    } else {

                        //throw new \Exception(sprintf('State not found %s, %s (%s - %s)', $city->getName(), $city->getGeonameIdentifier(), $city->getCountry()->getCode(), $city->getStateAdminCode()), 404);

                    }

                }

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

                }
                else {

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

    /**
     * @return string
     */
    public function getDefaultCountryCode()
    {
        return $this->defaultCountryCode;
    }

    /**
     * @param string $defaultCountryCode
     */
    public function setDefaultCountryCode($defaultCountryCode)
    {
        $this->defaultCountryCode = $defaultCountryCode;
    }

}