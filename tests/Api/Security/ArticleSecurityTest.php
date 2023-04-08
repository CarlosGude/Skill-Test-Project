<?php


namespace App\Tests\Api\Security;


use App\Entity\Article;
use App\Entity\User;
use App\Tests\Api\AbstractTest;
use App\Tests\Api\PublicRequest\ArticleTest;

/**
 * Class ArticleSecurityTest
 * @package App\Tests\Api\Security
 *
 */
class ArticleSecurityTest extends AbstractTest
{

    public function testPostArticle(): void
    {
        $response = $this->makeRequest(self::METHOD_POST,ArticleTest::API_ARTICLE,[
            'title' => 'TITLE TEST',
            'body' => 'TEST BODY',
        ]);

        $body = $response->toArray();

        $this->assertEquals(201,$response->getStatusCode());
        $this->assertEquals('TITLE TEST',$body['title']);
        $this->assertEquals('TEST BODY',$body['body']);

        $this->assertEquals($this->logins['admin']['email'],$body['author']['email']);
    }

    public function testPutArticle(): void
    {
        $user = $this->getRepository(User::class)->findOneBy(['email' => $this->logins['admin']['email']]);
        $articles = $this->getRepository(Article::class)->findBy(['user' => $user]);

        $this->assertGreaterThan(0,$articles);

        /** @var Article $article */
        $article = $articles[0];

        $response = $this->makeRequest(self::METHOD_PUT,ArticleTest::API_ARTICLE.'/'.$article->getUuid(),[
            'title' => 'TITLE TEST UPDATED',
        ]);
        $this->assertEquals(200,$response->getStatusCode());
        $body = $response->toArray();
        $this->assertEquals($this->logins['admin']['email'],$body['author']['email']);
        $this->assertEquals('TITLE TEST UPDATED',$body['title']);

    }

    public function testTheUserOnlyCanEditHisArticles(): void
    {
        $user = $this->getRepository(User::class)->findOneBy(['email' => 'test@email.test']);
        $articles = $this->getRepository(Article::class)->findBy(['user' => $user]);

        $this->assertGreaterThan(0,$articles);

        /** @var Article $article */
        $article = $articles[0];

        $response = $this->makeRequest(self::METHOD_PUT,ArticleTest::API_ARTICLE.'/'.$article->getUuid(),[
            'title' => 'TITLE TEST UPDATED'
        ]);

        $this->assertEquals(403,$response->getStatusCode());

    }


    public function testDeleteArticle(): void
    {
        $user = $this->getRepository(User::class)->findOneBy(['email' => 'admin@email.test']);
        $articles = $this->getRepository(Article::class)->findBy(['user' => $user]);

        $this->assertGreaterThan(0,$articles);

        /** @var Article $article */
        $article = $articles[0];

        $response = $this->makeRequest(self::METHOD_DELETE,ArticleTest::API_ARTICLE.'/'.$article->getUuid());

        $this->assertEquals(204,$response->getStatusCode());

    }

    public function testTheUserOnlyCanDeleteHisArticles(): void
    {
        $user = $this->getRepository(User::class)->findOneBy(['email' => 'test@email.test']);
        $articles = $this->getRepository(Article::class)->findBy(['user' => $user]);

        $this->assertGreaterThan(0,$articles);

        /** @var Article $article */
        $article = $articles[0];

        $response = $this->makeRequest(self::METHOD_DELETE,ArticleTest::API_ARTICLE.'/'.$article->getUuid());

        $this->assertEquals(403,$response->getStatusCode());

    }
}