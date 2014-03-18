<?php

namespace Shop\CatalogBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminCategoryControllerTest extends WebTestCase
{
    public function testCategories()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/categories');
    }

    public function testCategory()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/category/{id}');
    }

    public function testDeletecategory()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/deleteCategory/{id}');
    }

    public function testCategoryparameter()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/categoryParameter/{categoryId}/{id}');
    }

    public function testDeletecategoryparameter()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/deleteCategoryParameter/{id}');
    }

    public function testUpdatecategoryparameters()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/updateCategoryParameters/{categoryId}');
    }

}
