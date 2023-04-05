<?php

namespace App\Tests\Api\Success;


use App\Tests\Api\AbstractTest;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class UserTest extends KernelTestCase
{
    public const API_USER = 'api/user';

    protected function setUp(): void
    {
        AbstractTest::setUp();
    }

    public function testGetListOfUsers(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        /** @var HttpClientInterface $httpClient */
        $httpClient = $container->get(HttpClientInterface::class);
        $response = $httpClient->request('GET',AbstractTest::getBaseUrl().self::API_USER);
        $body = $response->toArray();

        $this->assertEquals(200,$response->getStatusCode());
        $this->assertCount(1,$body);
        $this->assertNotNull($body[0]['email']);
        $this->assertNotNull($body[0]['name']);

    }

    public function testGetDataOfUser(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        /** @var HttpClientInterface $httpClient */
        $httpClient = $container->get(HttpClientInterface::class);
        $response = $httpClient->request('GET','http://localhost/api/user/1');
        $body = $response->toArray();

        $this->assertEquals(200,$response->getStatusCode());
        $this->assertNotNull($body['email']);
        $this->assertNotNull($body['name']);

    }

    public function testPostOfUser(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        /** @var HttpClientInterface $httpClient */
        $httpClient = $container->get(HttpClientInterface::class);
        $response = $httpClient->request('POST',AbstractTest::getBaseUrl().self::API_USER,[
            'json' => [
                'name' => 'test',
                'email' => 'email@test.com',
                'password' => 'password1'
            ]
        ]);
        $body = $response->toArray();

        $this->assertEquals(201,$response->getStatusCode());
        $this->assertNotNull($body['email']);
        $this->assertNotNull($body['name']);

    }

    public function testPutUser(): void
    {
        $this->assertTrue(false);
    }

    public function testDeletedUser(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        /** @var HttpClientInterface $httpClient */
        $httpClient = $container->get(HttpClientInterface::class);
        $response = $httpClient->request('DELETE','http://localhost/api/user/1');

        $this->assertEquals(204,$response->getStatusCode());

    }
}
