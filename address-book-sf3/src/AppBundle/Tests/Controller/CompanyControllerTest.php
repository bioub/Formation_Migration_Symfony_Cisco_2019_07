<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CompanyControllerTest extends WebTestCase
{
    public function testList()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/company/');
    }

    public function testShow()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/company/{id}');
    }

}
