<?php

namespace Shop\CatalogBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminManufacturerControllerTest extends WebTestCase
{
    public function testManufacturers()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/manufacturers');
    }

    public function testManufacturer()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/manufacturer/{id}');
    }

    public function testDeletemanufacturer()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/deleteManufacturer/{id}');
    }

}
