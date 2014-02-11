<?php
namespace Shop\MainBundle\Utils;

/**
 * Class WordInflector
 * @package Shop\MainBundle\Utils
 */
class WordInflector {

    const CASE_NOMINATIVE = 1;
    const CASE_GENITIVE = 2;
    const CASE_DATIVE = 3;
    const CASE_ACCUSATIVUS = 4;
    const CASE_ABLATIVUS = 5;
    const CASE_PRAEPOSITIONALIS = 6;

    /**
     * @param $word
     * @param null|int $case
     * @return array|string|bool
     */
    public static function inflect($word, $case = null){

        $result = false;

        $json_response = @file_get_contents('http://export.yandex.ru/inflect.xml?name=' . urlencode($word) . '&format=json');
        if($json_response){

            $response = json_decode($json_response, true);

            if(is_array($response)){

                if($case){

                    if(isset($response[$case])){

                        $result = $response[$case];

                    }

                } else {

                    $result = $response;

                }

            }

        }

        return $result;

    }

} 