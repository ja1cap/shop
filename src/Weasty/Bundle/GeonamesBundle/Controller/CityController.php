<?php

namespace Weasty\Bundle\GeonamesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Weasty\Bundle\GeonamesBundle\Entity\City;

/**
 * Class CityController
 * @package Weasty\Bundle\GeonamesBundle\Controller
 */
class CityController extends Controller
{

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function cityLocatorAction(Request $request)
    {

        $latitude = $request->get('latitude');
        $longitude = $request->get('longitude');

        $cityLocator = $this->get('weasty_geonames.city.locator');
        $city = $cityLocator->locateCity($latitude, $longitude);

        $cityJsonData = null;

        if($city instanceof City){

            $cityJsonData = array(
                'id' => $city->getID(),
                'latitude' => $city->getLatitude(),
                'longitude' => $city->getLongitude(),
                'geonameIdentifier' => $city->getGeonameIdentifier(),
                'name' => $city->getLocaleName($this->container->getParameter('locale')),
                'country' => $city->getCountry() ? array(
                    'id' => $city->getCountry()->getID(),
                    'code' => $city->getCountry()->getCode(),
                    'name' => $city->getCountry()->getLocaleName($this->container->getParameter('locale')),
                    'geonameIdentifier' => $city->getCountry()->getGeonameIdentifier(),
                ) : null,
                'state' => $city->getState() ? array(
                    'id' => $city->getState()->getID(),
                    'name' => $city->getState()->getLocaleName($this->container->getParameter('locale')),
                    'geonameIdentifier' => $city->getState()->getGeonameIdentifier(),
                ) : null,
            );

        }

        $response = new JsonResponse($cityJsonData);

        return $response;

    }

    /**
     * @return \Weasty\Bundle\GeonamesBundle\Entity\CityRepository
     * @throws \LogicException
     */
    public function getCityRepository(){
        return $this->getDoctrine()->getRepository('WeastyGeonamesBundle:City');
    }

}
