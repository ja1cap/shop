<?php
namespace Weasty\GeonamesBundle\Data;

/**
 * Class GeonameLoader
 * @package Weasty\GeonamesBundle\Data
 */
class GeonameLoader {

    const API_FORMAT_JSON = 'json';
    const API_FORMAT_XML = 'xml';

    const API_XML_URL_TEMPLATE = "http://api.geonames.org/get?geonameId=:geonameId&username=:username&style=full";
    const API_JSON_URL_TEMPLATE = "http://api.geonames.org/getJSON?geonameId=:geonameId&username=:username&style=full";

    /**
     * @var string
     */
    protected $geonameUsername;

    /**
     * @var string
     */
    protected $geonameApiFormat;

    /**
     * @var string
     */
    protected $geonameApiUrlTemplate;

    function __construct($geonameUsername, $geonameApiFormat = self::API_FORMAT_JSON)
    {

        $this->geonameApiFormat = $geonameApiFormat;
        $this->geonameUsername = $geonameUsername;

        switch($this->geonameApiFormat){
            case self::API_FORMAT_JSON:
                $this->geonameApiUrlTemplate = self::API_JSON_URL_TEMPLATE;
                break;
            case self::API_FORMAT_XML:
                $this->geonameApiUrlTemplate = self::API_XML_URL_TEMPLATE;
                break;
            default:
                throw new \Exception(__CLASS__ . ' invalid geoname api format');
        }

        $replacements = array(
            ':username' => $geonameUsername,
        );

        $this->geonameApiUrlTemplate = str_replace(
            array_keys($replacements),
            $replacements,
            $this->geonameApiUrlTemplate
        );

    }

    /**
     * @param $geonameId
     * @return mixed|\SimpleXMLElement
     */
    public function load($geonameId){

        $geonameLocalityUrl = str_replace(':geonameId', (int)$geonameId, $this->geonameApiUrlTemplate);
        $geonameLocalityResponse = @file_get_contents($geonameLocalityUrl);

        switch($this->geonameApiFormat){
            case self::API_FORMAT_XML:

                $geonameLocalityData = new \SimpleXMLElement($geonameLocalityResponse);
                break;

            case self::API_FORMAT_JSON:
            default:

                $geonameLocalityData = json_decode($geonameLocalityResponse, true);

        }

        return $geonameLocalityData;

    }

} 