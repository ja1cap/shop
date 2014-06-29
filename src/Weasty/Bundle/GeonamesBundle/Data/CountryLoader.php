<?php
namespace Weasty\Bundle\GeonamesBundle\Data;

use Weasty\Bundle\GeonamesBundle\Entity\CountryRepository;
use JJs\Bundle\GeonamesBundle\Data\CountryLoader as BaseCountryLoader;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Class CountryLoader
 * @package Weasty\Bundle\GeonamesBundle\Data
 */
class CountryLoader extends BaseCountryLoader {

    /**
     * @param null $file
     * @param LoggerInterface $log
     */
    public function load($file = null, LoggerInterface $log = null)
    {

        $file = $file ?: static::DEFAULT_FILE;
        $log  = $log ?: new NullLogger();

        /**
         * @var $countryRepository CountryRepository
         */
        $countryRepository = $this->getCountryRepository();

        $columnsProperties = array(
            self::COLUMN_ISO_CODE => 'code',
            self::COLUMN_NAME => 'name',
            self::COLUMN_CAPITAL => 'capital',
            self::COLUMN_TOP_LEVEL_DOMAIN => 'domain',
            self::COLUMN_POSTAL_CODE_FORMAT => 'postalCodeFormat',
            self::COLUMN_POSTAL_CODE_REGEX => 'postalCodeRegex',
            self::COLUMN_PHONE => 'phonePrefix',
            self::COLUMN_GEONAME_ID => 'geonameIdentifier',
        );

        // Log an informational message
        $log->info("Loading country data from {file} into repository {repository}", [
            'file'       => $file,
            'repository' => get_class($countryRepository),
        ]);

        // Open the tab separated file for reading
        $tsv = fopen($file, 'r');
        while(false !== $data = fgetcsv($tsv, 0, "\t")) {

            // Skip all commented codes
            if (substr($data[0], 0, 1) === '#') continue;

            $categoryProperties = array();

            foreach($data as $column => $value){

                if(isset($columnsProperties[$column])){
                    $propertyName = $columnsProperties[$column];
                    $categoryProperties[$propertyName] = $value;
                }

            }

            // Log the process
            $log->info("{code} ({name})", $categoryProperties);

            $country = new Country();
            foreach($categoryProperties as $propertyName => $value){
                $country[$propertyName] = $value;
            }

            $countryRepository->saveCountry($country);

        }

    }

} 