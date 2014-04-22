<?php

namespace Weasty\GeonamesBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CityControllerTest extends WebTestCase
{
    public function testCitylocator()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/weastyGeonames/city/locator');
    }

}
