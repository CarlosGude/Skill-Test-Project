<?php


namespace App\Tests\Api\Security;


use App\Entity\User;
use App\Tests\Api\AbstractTest;
use App\Tests\Api\Success\UserTest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class UserSecurityTest extends KernelTestCase
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
                'email' => 'carlos@gmail.com',
                'password' => 'carlos@gmail.com'
            ]
        ]);
        $body = $response->toArray();
        $this->assertEquals(200,$response->getStatusCode());

        return $body['token'];
    }

    public function testPutUser(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        /** @var HttpClientInterface $httpClient */
        $httpClient = $container->get(HttpClientInterface::class);
        $token = $this->getToken();
        $response = $httpClient->request('PUT',AbstractTest::getBaseUrl().UserTest::API_USER.'/1',[
            'headers' =>['Authorization' =>  'bearer '.$token],
            'json' => ['name' => 'TEST CHANGE NAME']
        ]);

        $body = $response->toArray();
        $this->assertEquals(200,$response->getStatusCode());
        $this->assertEquals('TEST CHANGE NAME',$body['name']);

    }

    public function testTheUserAuthorizedCanNotEditAnotherUser(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        /** @var HttpClientInterface $httpClient */
        $httpClient = $container->get(HttpClientInterface::class);

        /** @var EntityManagerInterface $manager */
        $manager = $container->get(EntityManagerInterface::class);

        /** @var User $anotherUser */
        $anotherUser = $manager->getRepository(User::class)->findOneBy(['email' => 'another@gmail.com']);

        $token = $this->getToken();
        $response = $httpClient->request('PUT',AbstractTest::getBaseUrl().UserTest::API_USER.'/'.$anotherUser->getId(),[
            'headers' =>['Authorization' =>  'bearer '.$token],
            'json' => ['name' => 'TEST CHANGE NAME']
        ]);

        $this->assertEquals(403,$response->getStatusCode());

    }

    public function testSuccessLogin():void
    {
        $token = $this->getToken();
        $token = json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1]))));
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

    public function testDeletedUser(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        /** @var HttpClientInterface $httpClient */
        $httpClient = $container->get(HttpClientInterface::class);
        $token = $this->getToken();
        $response = $httpClient->request('DELETE',AbstractTest::getBaseUrl().UserTest::API_USER.'/1',[
            'headers' =>['Authorization' =>  'bearer '.$token]
        ]);

        $this->assertEquals(204,$response->getStatusCode());

    }

    public function testAnUserCanDeleteAntherUser(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        /** @var HttpClientInterface $httpClient */
        $httpClient = $container->get(HttpClientInterface::class);

        /** @var EntityManagerInterface $manager */
        $manager = $container->get(EntityManagerInterface::class);

        /** @var User $anotherUser */
        $anotherUser = $manager->getRepository(User::class)->findOneBy(['email' => 'another@gmail.com']);

        $token = $this->getToken();
        $response = $httpClient->request('DELETE',AbstractTest::getBaseUrl().UserTest::API_USER.'/'.$anotherUser->getId(),[
            'headers' =>['Authorization' =>  'bearer '.$token]
        ]);

        $this->assertEquals(403,$response->getStatusCode());

    }

    public function testDeleteNotFound():void
    {
        self::bootKernel();
        $container = static::getContainer();

        /** @var HttpClientInterface $httpClient */
        $httpClient = $container->get(HttpClientInterface::class);

        $token = $this->getToken();
        $response = $httpClient->request('DELETE',AbstractTest::getBaseUrl().UserTest::API_USER.'/1',[
            'headers' =>['Authorization' =>  'bearer '.$token]
        ]);

        $this->assertEquals(204,$response->getStatusCode());
        $response = $httpClient->request('GET','http://localhost/api/user/1');

        $this->assertEquals(404,$response->getStatusCode());

    }

    public function testUpdatePassword(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        /** @var EntityManagerInterface $manager */
        $manager = $container->get(EntityManagerInterface::class);

        /** @var User $authorizedUser */
        $authorizedUser = $manager->getRepository(User::class)->findOneBy(['email' => 'carlos@gmail.com']);
        $oldPassword = $authorizedUser->getPassword();

        /** @var HttpClientInterface $httpClient */
        $httpClient = $container->get(HttpClientInterface::class);
        $token = $this->getToken();
        $response = $httpClient->request('PUT',AbstractTest::getBaseUrl().UserTest::API_USER.'/1',[
            'headers' =>['Authorization' =>  'bearer '.$token],
            'json' => ['password' => 'TEST_CHANGE_PASSWORD']
        ]);

        $authorizedUser = $manager->getRepository(User::class)->findOneBy(['email' => 'carlos@gmail.com']);
        $this->assertEquals(200,$response->getStatusCode());
        $this->assertNotEquals($authorizedUser->getPassword(),$oldPassword);

    }
}