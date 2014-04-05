<?php
namespace Weasty\GeonamesBundle\Resources;

/**
 * Interface TranslatableLocality
 * @package Weasty\GeonamesBundle\Resources
 */
interface TranslatableGeoname {

    /**
     * Set localeNames
     *
     * @param array $localeNames
     * @return $this
     */
    public function setLocaleNames($localeNames);

    /**
     * Get localeNames
     *
     * @return array
     */
    public function getLocaleNames();

    /**
     * @param $locale
     * @param $name
     * @return $this
     */
    public function addLocaleName($locale, $name);

    /**
     * @param $locale
     * @return string
     */
    public function getLocaleName($locale);

    /**
     * @param $locale
     * @return $this
     */
    public function removeLocaleName($locale);


} 