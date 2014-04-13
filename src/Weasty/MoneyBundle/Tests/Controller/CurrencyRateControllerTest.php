<?php

namespace Weasty\MoneyBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CurrencyRateControllerTest extends WebTestCase
{
    public function testRates()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/currencyRate/rates');
    }

    public function testRate()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/currencyRate/rate/{id}');
    }

}
