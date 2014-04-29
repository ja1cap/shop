<?php

namespace Shop\CatalogBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminPopularProposalControllerTest extends WebTestCase
{
    public function testProposals()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/adminCatalog/popularProposals');
    }

    public function testProposal()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/adminCatalog/popularProposal/{id}');
    }

}
