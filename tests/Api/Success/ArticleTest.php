<?php

namespace App\Tests\Api\Success;


use App\Entity\Article;
use App\Entity\User;
use App\Tests\Api\AbstractTest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class ArticleTest
 * @package App\Tests\Api\Success
 * TODO: Refactorizar esta mierda antes de que me den ganas de arrancarme los ojos.
 */
class ArticleTest extends KernelTestCase
{
    public const API_ARTICLE = 'api/article';

    protected function setUp(): void
    {
        AbstractTest::setUp();
    }

    public function testGetListOfArticles(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        /** @var HttpClientInterface $httpClient */
        $httpClient = $container->get(HttpClientInterface::class);
        $response = $httpClient->request('GET',AbstractTest::getBaseUrl().self::API_ARTICLE);
        $body = $response->toArray();

        $this->assertEquals(200,$response->getStatusCode());
        $this->assertGreaterThan(1,$body);
        $this->assertNotNull($body[0]['title']);
        $this->assertNotNull($body[0]['body']);
        $this->assertNotNull($body[0]['author']);
        $this->assertIsArray($body[0]['author']);
        $this->assertNotNull($body[0]['author']['name']);
        $this->assertNotNull($body[0]['author']['email']);

    }

    public function testGetArticle(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        /** @var HttpClientInterface $httpClient */
        $httpClient = $container->get(HttpClientInterface::class);

        /** @var EntityManagerInterface $manager */
        $manager = $container->get(EntityManagerInterface::class);

        $user = $manager->getRepository(User::class)->findOneBy(['email' => 'carlos@gmail.com']);
        $articles = $manager->getRepository(Article::class)->findBy(['user' => $user]);

        $this->assertGreaterThan(0,$articles);
        /** @var Article $article */
        $article = $articles[0];

        $response = $httpClient->request('GET',AbstractTest::getBaseUrl().self::API_ARTICLE.'/'.$article->getId());
        $body = $response->toArray();

        $this->assertEquals(200,$response->getStatusCode());
        $this->assertNotNull($body['title']);
        $this->assertNotNull($body['body']);
        $this->assertNotNull($body['author']);
        $this->assertIsArray($body['author']);
        $this->assertNotNull($body['author']['name']);
        $this->assertNotNull($body['author']['email']);

    }

    public function testCreateArticleWithoutLogin(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        /** @var HttpClientInterface $httpClient */
        $httpClient = $container->get(HttpClientInterface::class);
        $response = $httpClient->request('POST',AbstractTest::getBaseUrl().self::API_ARTICLE,[
            'json' => [
                'title' => 'TITLE TEST',
                'body' => 'BODY TEST'
            ]
        ]);

        $this->assertEquals(401,$response->getStatusCode());

    }

    public function testDeleteArticleWithoutLogin(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        /** @var HttpClientInterface $httpClient */
        $httpClient = $container->get(HttpClientInterface::class);

        /** @var EntityManagerInterface $manager */
        $manager = $container->get(EntityManagerInterface::class);

        $articles = $manager->getRepository(Article::class)->findAll();

        $this->assertGreaterThan(0,$articles);
        /** @var Article $article */
        $article = $articles[0];

        $response = $httpClient->request('DELETE',AbstractTest::getBaseUrl().self::API_ARTICLE.'/'.$article->getId());

        $this->assertEquals(401,$response->getStatusCode());

    }
}
