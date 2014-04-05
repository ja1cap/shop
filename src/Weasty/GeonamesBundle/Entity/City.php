<?php

namespace Weasty\GeonamesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JJs\Bundle\GeonamesBundle\Entity\State;
use Weasty\GeonamesBundle\Resources\TranslatableGeoname;

/**
 * City
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Weasty\GeonamesBundle\Entity\CityRepository")
 */
class City extends Locality implements TranslatableGeoname
{

    /**
     * City locale names
     * @ORM\Column(name="locale_names", type="json_array", nullable=true)
     * @var array
     */
    protected $localeNames;

    /**
     * City population
     * @ORM\Column(name="population", type="integer", nullable=true)
     * @var integer
     */
    protected $population;

    /**
     * State
     *
     * @ORM\ManyToOne(targetEntity="JJs\Bundle\GeonamesBundle\Entity\State")
     * @var State
     */
    protected $state;

    /**
     * @param int $population
     * @return $this
     */
    public function setPopulation($population)
    {
        $this->population = $population;
        return $this;
    }

    /**
     * @return int
     */
    public function getPopulation()
    {
        return $this->population;
    }

    /**
     * @param array $localeNames
     * @return $this
     */
    public function setLocaleNames($localeNames)
    {
        $this->localeNames = $localeNames;
        return $this;
    }

    /**
     * @return array
     */
    public function getLocaleNames()
    {
        return $this->localeNames;
    }

    /**
     * @param $locale
     * @param $name
     * @return $this
     */
    public function addLocaleName($locale, $name){
        $this->localeNames[$locale] = $name;
        return $this;
    }

    /**
     * @param $locale
     * @return string
     */
    public function getLocaleName($locale){
        if(isset($this->localeNames[$locale])){
            return $this->localeNames[$locale];
        }
        return $this->getName();
    }

    /**
     * @param $locale
     * @return $this
     */
    public function removeLocaleName($locale){
        if(isset($this->localeNames[$locale])){
            unset($this->localeNames[$locale]);
        }
        return $this;
    }

}
