<?php

namespace App\Tests\Api\Validation;

use App\Tests\Api\AbstractTest;
use App\Tests\Api\PublicRequest\UserTest;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class UserValidationTest.
 */
class UserValidationTest extends AbstractTest
{
    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testEmailError(): void
    {
        $response = $this->makeRequest(self::METHOD_POST, UserTest::API_USER, [
            'name' => 'TEST CHANGE NAME',
            'email' => 'INVALID',
            'password' => 'TEST_PASSWORD',
        ]);

        $body = $response->toArray(false);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertArrayHasKey('email', $body);
        $this->assertEquals('This value is not a valid email address.', $body['email']);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testPasswordError(): void
    {
        $response = $this->makeRequest(self::METHOD_POST, UserTest::API_USER, [
            'name' => 'TEST CHANGE NAME',
            'email' => 'INVALID',
            'password' => 'pass',
        ]);

        $body = $response->toArray(false);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertArrayHasKey('password', $body);
        $this->assertEquals('This value is not valid.', $body['password']);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testEmailExistError(): void
    {
        $response = $this->makeRequest(self::METHOD_POST, UserTest::API_USER, [
            'name' => 'TEST CHANGE NAME',
            'email' => 'admin@email.test',
            'password' => 'pass',
        ]);

        $body = $response->toArray(false);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertArrayHasKey('email', $body);
        $this->assertEquals('This email is already in use.', $body['email']);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testNameNotSent(): void
    {
        $response = $this->makeRequest(self::METHOD_POST, UserTest::API_USER, [
            'email' => 'test_created@email.com',
            'password' => 'TEST_PASSWORD',
        ]);

        $body = $response->toArray(false);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertArrayHasKey('name', $body);
        $this->assertEquals('This value should not be null.', $body['name']);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testEmailNotSent(): void
    {
        $response = $this->makeRequest(self::METHOD_POST, UserTest::API_USER, [
            'name' => 'TEST CHANGE NAME',
            'password' => 'TEST_PASSWORD',
        ]);

        $body = $response->toArray(false);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertArrayHasKey('email', $body);
        $this->assertEquals('This value should not be null.', $body['email']);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testPasswordNotSent(): void
    {
        $response = $this->makeRequest(self::METHOD_POST, UserTest::API_USER, [
            'name' => 'TEST CHANGE NAME',
            'email' => 'test_created@email.com',
        ]);

        $body = $response->toArray(false);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertArrayHasKey('password', $body);
        $this->assertEquals('This value should not be null.', $body['password']);
    }
}
