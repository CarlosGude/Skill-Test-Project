<?php


namespace App\Tests\Api\Validation;

use App\Tests\Api\AbstractTest;
use App\Tests\Api\Success\ArticleTest;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class ArticleValidationTest
 * @package App\Tests\Api\Validation
 * TODO: Refactorizar esta mierda antes de que me den ganas de arrancarme los ojos.
 */
class ArticleValidationTest extends KernelTestCase
{

    public const API_LOGIN = 'api/login_check';

    protected function setUp(): void
    {
        AbstractTest::setUp();
    }

    protected function getToken(): string
    {
        self::bootKernel();
        $container = static::getContainer();

        /** @var HttpClientInterface $httpClient */
        $httpClient = $container->get(HttpClientInterface::class);

        $response = $httpClient->request('POST',AbstractTest::getBaseUrl().self::API_LOGIN,[
            'json' => [
                'email' => 'admin@email.test',
                'password' => 'password1admin'
            ]
        ]);
        $body = $response->toArray();
        $this->assertEquals(200,$response->getStatusCode());

        return $body['token'];
    }

    public function testTitleError():void
    {
        self::bootKernel();
        $container = static::getContainer();

        /** @var HttpClientInterface $httpClient */
        $httpClient = $container->get(HttpClientInterface::class);
        $token = $this->getToken();
        $response = $httpClient->request('POST',AbstractTest::getBaseUrl().ArticleTest::API_ARTICLE,[
            'headers' =>['Authorization' =>  'bearer '.$token],
            'json' => ['body' => 'TITLE BODY']
        ]);

        $body = $response->toArray(false);
        $this->assertEquals(400,$response->getStatusCode());
        $this->assertArrayHasKey('title',$body);
        $this->assertEquals('This value should not be null.',$body['title']);

    }

    public function testBodyError():void
    {
        self::bootKernel();
        $container = static::getContainer();

        /** @var HttpClientInterface $httpClient */
        $httpClient = $container->get(HttpClientInterface::class);
        $token = $this->getToken();
        $response = $httpClient->request('POST',AbstractTest::getBaseUrl().ArticleTest::API_ARTICLE,[
            'headers' =>['Authorization' =>  'bearer '.$token],
            'json' => ['title' => 'TITLE BODY']
        ]);

        $body = $response->toArray(false);
        $this->assertEquals(400,$response->getStatusCode());
        $this->assertArrayHasKey('body',$body);
        $this->assertEquals('This value should not be null.',$body['body']);

    }

}