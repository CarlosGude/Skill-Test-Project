<?php

namespace App\Tests\Api\Success;


use App\Entity\Article;
use App\Tests\Api\AbstractTest;

/**
 * Class ArticleTest
 * @package App\Tests\Api\Success
 */
class ArticleTest extends AbstractTest
{
    public const API_ARTICLE = 'api/article';
    public array $article = ['title' => 'TITLE TEST', 'body' => 'BODY TEST'];

    public function testGetListOfArticles(): void
    {

        $response = $this->makeRequest(self::METHOD_GET,self::API_ARTICLE);
        $this->assertEquals(200,$response->getStatusCode());
        $this->assertGreaterThan(1,$body = $response->toArray());
        $this->assertNotNull($body[0]['title']);
        $this->assertNotNull($body[0]['body']);
        $this->assertNotNull($body[0]['author']);
        $this->assertIsArray($body[0]['author']);
        $this->assertNotNull($body[0]['author']['name']);
        $this->assertNotNull($body[0]['author']['email']);

    }

    public function testGetArticle(): void
    {
        $articles = $this->getRepository(Article::class)->findAll();
        $this->assertGreaterThan(1,count($articles));

        /** @var Article $article */
        $article = $articles[0];

        $response = $this->makeRequest(self::METHOD_GET,self::API_ARTICLE.'/'.$article->getId());
        $this->assertEquals(200,$response->getStatusCode());
        $this->assertGreaterThan(1,$body = $response->toArray());
        $this->assertNotNull($body['title']);
        $this->assertNotNull($body['body']);
        $this->assertNotNull($body['author']);
        $this->assertIsArray($body['author']);
        $this->assertNotNull($body['author']['name']);
        $this->assertNotNull($body['author']['email']);

    }

    public function testCreateArticleWithoutLogin(): void
    {
        $response = $this->makeRequest(self::METHOD_POST,self::API_ARTICLE,$this->article,null);
        $this->assertEquals(401,$response->getStatusCode());
    }

    public function testDeleteArticleWithoutLogin(): void
    {
        $articles = $this->getRepository(Article::class)->findAll();
        $this->assertGreaterThan(1,count($articles));

        /** @var Article $article */
        $article = $articles[0];

        $response = $this->makeRequest(self::METHOD_DELETE,self::API_ARTICLE.'/'.$article->getId(),[],null);
        $this->assertEquals(401,$response->getStatusCode());
    }
}
