<?php

namespace Shop\UserBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminUserControllerTest extends WebTestCase
{
    public function testUsers()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/users');
    }

    public function testUser()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/user/{id}');
    }

    public function testDeleteuser()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/deleteUser/{id}');
    }

}
