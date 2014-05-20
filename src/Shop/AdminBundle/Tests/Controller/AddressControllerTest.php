<?php

namespace Shop\AdminBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AddressControllerTest extends WebTestCase
{
    public function testAddresses()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/addresses');
    }

    public function testAddress()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/address/{id}');
    }

}
