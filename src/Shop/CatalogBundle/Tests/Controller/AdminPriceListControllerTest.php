<?php

namespace Shop\CatalogBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminPriceListControllerTest extends WebTestCase
{
    public function testPricelists()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/priceLists');
    }

    public function testParsepricelist()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/parsePriceList/{id}');
    }

    public function testDeletepricelist()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/deletePriceList/{id}');
    }

}
