<?php

namespace Shop\CatalogBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EstimatorControllerTest extends WebTestCase
{
    public function testEstimator()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/catalog/estimator/{categorySlug}');
    }

}
