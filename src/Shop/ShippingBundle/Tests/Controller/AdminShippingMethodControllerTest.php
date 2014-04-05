<?php

namespace Shop\ShippingBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminShippingMethodControllerTest extends WebTestCase
{
    public function testShippingmethods()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/shippingMethods');
    }

    public function testShippingmethod()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/shippingMethod/{id}');
    }

    public function testDeleteshippingmethod()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/deleteShippingMethod/{id}');
    }

}
