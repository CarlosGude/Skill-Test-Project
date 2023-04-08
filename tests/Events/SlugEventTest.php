<?php

namespace App\Tests\Events;

use App\Entity\Article;
use App\Message\EntityEvent;
use App\Tests\Api\AbstractTest;
use App\Tests\Api\PublicRequest\ArticleTest;
use Symfony\Component\Messenger\Envelope;

class SlugEventTest extends AbstractTest
{
    protected function findMessageExpected(string $uuid, string $eventType): ?EntityEvent
    {
        /** @var Envelope $item */
        foreach ($this->transport->all() as $item) {
            $message = $item->getMessage();
            if ($message instanceof EntityEvent && $uuid === $message->uuid && $eventType === $message->getEvent()) {
                return $message;
            }
        }

        return null;
    }

    public function testCreateArticleGenerateSlugEvent(): void
    {
        $response = $this->makeRequest(self::METHOD_POST, ArticleTest::API_ARTICLE, [
            'title' => 'TITLE TEST',
            'body' => 'TEST BODY',
        ]);

        $body = $response->toArray();

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals('TITLE TEST', $body['title']);
        $this->assertEquals('TEST BODY', $body['body']);

        $this->assertEquals($this->logins['admin']['email'], $body['author']['email']);

        /** @var Article $article */
        $article = $this->getRepository(Article::class)->findOneBy(['uuid' => $body['uuid']]);

        $this->assertCount(1, $this->transport->get());
        $this->assertInstanceOf(EntityEvent::class, $message = $this->findMessageExpected($body['uuid'], EntityEvent::EVENT_CREATE));
        $this->assertNull($article->getSlug());

        $this->slugEventHandler->__invoke($message);

        // Reload article for see the slug
        /** @var Article $article */
        $article = $this->getRepository(Article::class)->findOneBy(['uuid' => $article->getUuid()]);

        $this->assertNotNull($article->getSlug());
        $this->assertEquals('title-test', $article->getSlug());
    }

    public function testCreateArticleWithTitleExistAddCountToGenerateSlugEvent(): void
    {
        $articles = $this->getRepository(Article::class)->findAll();
        $existTitle = $articles[0]->getTitle();
        $existSlug = $articles[0]->getSlug();

        $response = $this->makeRequest(self::METHOD_POST, ArticleTest::API_ARTICLE, [
            'title' => $existTitle,
            'body' => 'TEST BODY',
        ]);

        $body = $response->toArray();

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals($existTitle, $body['title']);
        $this->assertEquals('TEST BODY', $body['body']);

        $this->assertEquals($this->logins['admin']['email'], $body['author']['email']);

        /** @var Article $article */
        $article = $this->getRepository(Article::class)->findOneBy(['uuid' => $body['uuid']]);

        $this->assertCount(1, $this->transport->get());
        $this->assertInstanceOf(EntityEvent::class, $message = $this->findMessageExpected($article->getUuid(), EntityEvent::EVENT_CREATE));
        $this->assertNull($article->getSlug());

        $this->slugEventHandler->__invoke($message);

        // Reload article for see the slug
        /** @var Article $article */
        $article = $this->getRepository(Article::class)->findOneBy(['uuid' => $article->getUuid()]);

        $this->assertNotNull($article->getSlug());
        $this->assertEquals($existSlug.'-1', $article->getSlug());
    }
}
