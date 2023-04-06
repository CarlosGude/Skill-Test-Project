<?php


namespace App\Tests\Api\Security;


use App\Tests\Api\AbstractTest;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class UserSecurityTest extends KernelTestCase
{
    public const API_LOGIN = 'api/login_check';

    protected function setUp(): void
    {
        AbstractTest::setUp();
    }

    public function testAnUserCanEditAnotherUser():void
    {
        $this->assertTrue(false);
    }

    public function testSuccessLogin():void
    {
        self::bootKernel();
        $container = static::getContainer();

        /** @var HttpClientInterface $httpClient */
        $httpClient = $container->get(HttpClientInterface::class);

        $response = $httpClient->request('POST',AbstractTest::getBaseUrl().self::API_LOGIN,[
            'json' => [
                'email' => 'carlos@gmail.com',
                'password' => 'carlos@gmail.com'
            ]
        ]);
        $body = $response->toArray();
        $this->assertEquals(200,$response->getStatusCode());
        $token = json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $body['token'])[1]))));
        $this->assertEquals('carlos@gmail.com',$token->username);
    }

    public function testFailureLogin():void
    {
        self::bootKernel();
        $container = static::getContainer();

        /** @var HttpClientInterface $httpClient */
        $httpClient = $container->get(HttpClientInterface::class);
        $response = $httpClient->request('POST',AbstractTest::getBaseUrl().self::API_LOGIN,[
            'json' => [
                'email' => 'carlos@gmail.com',
                'password' => 'fail'
            ]
        ]);
        $this->assertEquals(401,$response->getStatusCode());
    }
}