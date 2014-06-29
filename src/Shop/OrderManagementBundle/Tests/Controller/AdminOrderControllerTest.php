<?php

namespace Shop\OrderManagementBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminOrderControllerTest extends WebTestCase
{
    public function testOrders()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/orders');
    }

    public function testOrder()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/order/{id}');
    }

}
