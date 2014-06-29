<?php

namespace Shop\CatalogBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminContractorControllerTest extends WebTestCase
{
    public function testContractors()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/contractors');
    }

    public function testContractor()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/contractor/{id}');
    }

    public function testDeletecontractor()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/deleteContractor');
    }

}
