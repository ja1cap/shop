<?php

namespace Weasty\Bundle\GeonamesBundle\Entity;

use JJs\Bundle\GeonamesBundle\Model\LocalityInterface;

/**
 * Class StateRepository
 * @package Weasty\Bundle\GeonamesBundle\Entity
 */
class StateRepository extends LocalityRepository
{

    /**
     * Returns a state
     *
     * @param mixed $state State
     *
     * @return State
     */
    public function getState($state)
    {
        if ($state instanceof State) return $state;
        if ($state instanceof LocalityInterface) return $this->getLocality($state);

        return $this->find($state);
    }

    /**
     * Imports a locality as a state
     *
     * @param LocalityInterface $locality Locality
     * @return State
     */
    public function importLocality(LocalityInterface $locality)
    {
        // No change is neccisasary for state instances
        if ($locality instanceof State) return $locality;

        // Load the existing state for the locality, or create a new instance
        $state = $this->getState($locality) ?: new State();

        // Copy data from the import locality into an existing or new state
        // instance
        $this->copyLocality($locality, $state);

        $geonameId = $locality->getGeonameIdentifier();
        $geonameData = $this->getGeonameLoader()->load($geonameId);

        $state->setAdminCode($geonameData['adminCode1']);

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

            $state->setLocaleNames($localeNames);

        }

        // Return the state instance from the locality
        return $state;
    }

}
