<?php

namespace Shop\MainBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/index');
    }

    public function testHitproposal()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/hitProposal');
    }

    public function testWhyus()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/whyUs');
    }

    public function testProposal()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/proposal');
    }

    public function testProposals()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/proposals');
    }

    public function testBenefits()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/benefits');
    }

    public function testReviews()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/reviews');
    }

    public function testImages()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/images');
    }

    public function testHowwe()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/howWe');
    }

    public function testProblems()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/problems');
    }

    public function testContacts()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/contacts');
    }

    public function testAddress()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/address');
    }

    public function testSettings()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/settings');
    }

}
