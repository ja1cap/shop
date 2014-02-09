<?php

namespace Shop\CatalogBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminProposalControllerTest extends WebTestCase
{
    public function testProposals()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/proposals/{categoryId}');
    }

    public function testProposal()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/proposa/{id}');
    }

    public function testDeleteproposal()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/deleteProposal/{id}');
    }

    public function testProposalprice()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/proposalPrice/{proposalId}/{id}');
    }

    public function testDeleteproposalprice()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/deleteProposalPrice/{id}');
    }

}
