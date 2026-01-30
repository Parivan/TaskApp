<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class HomeControllerTest extends WebTestCase
{
    public function testIndexIsSuccessful(): void
    {
        $client = static::createClient();

        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHeaderSame('content-type', 'text/html; charset=UTF-8');
    }

    public function testIndexWithTeamAllIsSuccessful(): void
    {
        $client = static::createClient();

        $client->request('GET', '/?team=all');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
    }

    public function testIndexWithTeamIdIsSuccessful(): void
    {
        $client = static::createClient();

        $client->request('GET', '/?team=1');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
    }
}
