<?php


namespace App\Tests\Api\Security;


use App\Entity\Article;
use App\Entity\User;
use App\Tests\Api\AbstractTest;
use App\Tests\Api\Success\ArticleTest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class ArticleSecurityTest
 * @package App\Tests\Api\Security
 *
 * TODO: Refactorizar esta mierda antes de que me den ganas de arrancarme los ojos.
 */
class ArticleSecurityTest extends KernelTestCase
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

    public function testPostArticle(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        /** @var HttpClientInterface $httpClient */
        $httpClient = $container->get(HttpClientInterface::class);
        $token = $this->getToken();
        $user = json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1]))));
        $response = $httpClient->request('POST',AbstractTest::getBaseUrl().ArticleTest::API_ARTICLE,[
            'headers' =>['Authorization' =>  'bearer '.$token],
            'json' => ['title' => 'TITLE TEST','body' => 'TITLE BODY']
        ]);

        $body = $response->toArray();
        $this->assertEquals(201,$response->getStatusCode());
        $this->assertEquals('TITLE TEST',$body['title']);
        $this->assertEquals('TITLE BODY',$body['body']);
        $this->assertEquals($user->username,$body['author']['email']);
    }

    public function testPutArticle(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        /** @var HttpClientInterface $httpClient */
        $httpClient = $container->get(HttpClientInterface::class);
        $token = $this->getToken();
        $tokenDecoded = json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1]))));

        self::bootKernel();
        $container = static::getContainer();

        /** @var EntityManagerInterface $manager */
        $manager = $container->get(EntityManagerInterface::class);

        /** @var User $user */
        $user = $manager->getRepository(User::class)->findOneBy(['email' => $tokenDecoded->username]);

        $articles = $manager->getRepository(Article::class)->findBy(['user' => $user]);

        $this->assertGreaterThan(0,$articles);

        /** @var Article $article */
        $article = $articles[0];

        $response = $httpClient->request('PUT',AbstractTest::getBaseUrl().ArticleTest::API_ARTICLE.'/'.$article->getId(),[
            'headers' =>['Authorization' =>  'bearer '.$token],
            'json' => ['title' => 'TITLE TEST','body' => 'TITLE BODY']
        ]);

        $body = $response->toArray();
        $this->assertEquals(200,$response->getStatusCode());
        $this->assertEquals('TITLE TEST',$body['title']);
        $this->assertEquals('TITLE BODY',$body['body']);
        $this->assertEquals($user->getEmail(),$body['author']['email']);
    }

    public function testTheUserOnlyCanEditHisArticles(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        /** @var HttpClientInterface $httpClient */
        $httpClient = $container->get(HttpClientInterface::class);

        /** @var EntityManagerInterface $manager */
        $manager = $container->get(EntityManagerInterface::class);

        /** @var User $anotherUser */
        $anotherUser = $manager->getRepository(User::class)->findOneBy(['email' => 'test@email.test']);
        $articles = $manager->getRepository(Article::class)->findBy(['user' => $anotherUser]);

        $this->assertGreaterThan(1,$articles);

        /** @var Article $article */
        $article = $articles[0];

        $token = $this->getToken();
        $response = $httpClient->request('PUT',AbstractTest::getBaseUrl().ArticleTest::API_ARTICLE.'/'.$article->getId(),[
            'headers' =>['Authorization' =>  'bearer '.$token],
            'json' => ['title' => 'TITLE EDIT']
        ]);

        $this->assertEquals(403,$response->getStatusCode());

    }


    public function testDeleteArticle(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        /** @var HttpClientInterface $httpClient */
        $httpClient = $container->get(HttpClientInterface::class);
        $token = $this->getToken();
        $tokenDecoded = json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1]))));

        self::bootKernel();
        $container = static::getContainer();

        /** @var EntityManagerInterface $manager */
        $manager = $container->get(EntityManagerInterface::class);

        /** @var User $user */
        $user = $manager->getRepository(User::class)->findOneBy(['email' => $tokenDecoded->username]);

        $articles = $manager->getRepository(Article::class)->findBy(['user' => $user]);

        $this->assertGreaterThan(0,$articles);

        /** @var Article $article */
        $article = $articles[0];

        $response = $httpClient->request('DELETE',AbstractTest::getBaseUrl().ArticleTest::API_ARTICLE.'/'.$article->getId(),[
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
        $anotherUser = $manager->getRepository(User::class)->findOneBy(['email' => 'test@email.test']);

        $articles = $manager->getRepository(Article::class)->findBy(['user' => $anotherUser]);

        $this->assertGreaterThan(1,$articles);

        /** @var Article $article */
        $article = $articles[0];

        $token = $this->getToken();

        $response = $httpClient->request('DELETE',AbstractTest::getBaseUrl().ArticleTest::API_ARTICLE.'/'.$article->getId(),[
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
        $tokenDecoded = json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1]))));

        self::bootKernel();
        $container = static::getContainer();

        /** @var EntityManagerInterface $manager */
        $manager = $container->get(EntityManagerInterface::class);

        /** @var User $user */
        $user = $manager->getRepository(User::class)->findOneBy(['email' => $tokenDecoded->username]);

        $articles = $manager->getRepository(Article::class)->findBy(['user' => $user]);

        $this->assertGreaterThan(0,$articles);

        /** @var Article $article */
        $article = $articles[0];

        $response = $httpClient->request('DELETE',AbstractTest::getBaseUrl().ArticleTest::API_ARTICLE.'/'.$article->getId(),[
            'headers' =>['Authorization' =>  'bearer '.$token]
        ]);

        $this->assertEquals(204,$response->getStatusCode());

        $response = $httpClient->request('GET',AbstractTest::getBaseUrl().ArticleTest::API_ARTICLE.'/'.$article->getId(),[
            'headers' =>['Authorization' =>  'bearer '.$token]
        ]);

        $this->assertEquals(404,$response->getStatusCode());
    }

}