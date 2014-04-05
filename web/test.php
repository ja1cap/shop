<?php

$geonameId = 625144;
$geonameUsername = "ja1cap";
$geonameLocalityUrl = "http://api.geonames.org/getJSON?geonameId=$geonameId&username=$geonameUsername&style=full";
$geonameLocalityJSON = @file_get_contents($geonameLocalityUrl);
$geonameLocalityData = json_decode($geonameLocalityJSON, true);

$localeNames = array();

foreach($geonameLocalityData['alternateNames'] as $alternateName){

    if($alternateName['lang'] && $alternateName['lang'] != 'link'){
        $localeNames[$alternateName['lang']] = $alternateName['name'];
    }

}

var_dump($localeNames);

die;