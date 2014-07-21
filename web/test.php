<?php

//var_dump(4 % 23);
//die;

$strUTF8 = 'ßABCDAÄъ123абвд';
$strUTF8 = 'ъ';

//var_dump(mb_convert_encoding($strUTF8, 'HTML-ENTITIES', 'UTF-8'));

function _uniord($c) {
    if (ord($c{0}) >=0 && ord($c{0}) <= 127)
        return ord($c{0});
    if (ord($c{0}) >= 192 && ord($c{0}) <= 223)
        return (ord($c{0})-192)*64 + (ord($c{1})-128);
    if (ord($c{0}) >= 224 && ord($c{0}) <= 239)
        return (ord($c{0})-224)*4096 + (ord($c{1})-128)*64 + (ord($c{2})-128);
    if (ord($c{0}) >= 240 && ord($c{0}) <= 247)
        return (ord($c{0})-240)*262144 + (ord($c{1})-128)*4096 + (ord($c{2})-128)*64 + (ord($c{3})-128);
    if (ord($c{0}) >= 248 && ord($c{0}) <= 251)
        return (ord($c{0})-248)*16777216 + (ord($c{1})-128)*262144 + (ord($c{2})-128)*4096 + (ord($c{3})-128)*64 + (ord($c{4})-128);
    if (ord($c{0}) >= 252 && ord($c{0}) <= 253)
        return (ord($c{0})-252)*1073741824 + (ord($c{1})-128)*16777216 + (ord($c{2})-128)*262144 + (ord($c{3})-128)*4096 + (ord($c{4})-128)*64 + (ord($c{5})-128);
    if (ord($c{0}) >= 254 && ord($c{0}) <= 255)    //  error
        return FALSE;
    return 0;
}

function utf8_to_unicode( $str ) {

    $unicode = array();
    $values = array();
    $lookingFor = 1;

    for ($i = 0; $i < strlen( $str ); $i++ ) {

        $currentValue = ord( $str[ $i ] );

        if ( $currentValue < 128 ){

            $unicode[] = $currentValue;

        } else {

            if ( count( $values ) == 0 ) {
                $lookingFor = ( $currentValue < 224 ) ? 2 : 3;
            }

            $values[] = $currentValue;

            if ( count( $values ) == $lookingFor ) {

                $number = ( $lookingFor == 3 ) ?
                    ( ( $values[0] % 16 ) * 4096 ) + ( ( $values[1] % 64 ) * 64 ) + ( $values[2] % 64 ):
                    ( ( $values[0] % 32 ) * 64 ) + ( $values[1] % 64 );

                $unicode[] = $number;
                $values = array();
                $lookingFor = 1;

            }

        }

    }

    return $unicode;

}

var_dump(_uniord($strUTF8));
var_dump(utf8_to_unicode($strUTF8));

die;

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