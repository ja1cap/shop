<?php

namespace Shop\CatalogBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminParameterControllerTest extends WebTestCase
{
    public function testParameters()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/parameters');
    }

    public function testParameter()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/parameter/{id}');
    }

    public function testDeleteparameter()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admi/deleteParameter/{id}');
    }

    public function testParameteroption()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/parameterOption/{parameterId}/{id}');
    }

    public function testDeleteparameteroption()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/parameterOption/{parameterId}/{id}');
    }

}
