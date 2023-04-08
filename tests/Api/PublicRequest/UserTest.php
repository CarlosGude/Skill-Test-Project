<?php

namespace App\Tests\Api\PublicRequest;


use App\Entity\User;
use App\Tests\Api\AbstractTest;

/**
 * Class UserTest
 * @package App\Tests\Api\PublicRequest
 */
class UserTest extends AbstractTest
{
    public const API_USER = '/api/user';
    public array $user = ['name' => 'test', 'email' => 'test@test.com', 'password' => 'password1'];

    public function testGetListOfUsers(): void
    {
        $response = $this->makeRequest(self::METHOD_GET,self::API_USER);
        $this->assertEquals(200,$response->getStatusCode());
        $this->assertGreaterThan(1,$body = $response->toArray());
        $this->assertNotNull($body[0]['email']);
        $this->assertNotNull($body[0]['name']);
        $this->assertIsArray($body[0]['articles']);

    }

    public function testGetUser(): void
    {
        $users = $this->getRepository(User::class)->findAll();
        $this->assertGreaterThan(1,count($users));

        /** @var User $user */
        $user = $users[0];

        $response = $this->makeRequest(self::METHOD_GET,self::API_USER.'/'.$user->getId());

        $this->assertEquals(200,$response->getStatusCode());
        $this->assertIsArray($body = $response->toArray());
        $this->assertNotNull($body['email']);
        $this->assertNotNull($body['name']);
        $this->assertIsArray($body['articles']);

    }

    public function testCreateUser(): void
    {
        $response = $this->makeRequest(self::METHOD_POST,self::API_USER,$this->user);

        $this->assertEquals(201,$response->getStatusCode());
        $this->assertIsArray($body = $response->toArray());
        $this->assertNotNull($body['email']);
        $this->assertNotNull($body['name']);

    }
}
