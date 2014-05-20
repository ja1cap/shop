<?php

namespace Shop\AdminBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PhoneControllerTest extends WebTestCase
{
    public function testPhones()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/phones');
    }

    public function testPhone()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/phone/{id}');
    }

}
