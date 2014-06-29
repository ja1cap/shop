<?php

namespace Shop\UserBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminManagerControllerTest extends WebTestCase
{
    public function testManagers()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/managers');
    }

    public function testManager()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/manager');
    }

    public function testDeletemanager()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/deleteManager');
    }

}
