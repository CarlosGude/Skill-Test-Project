<?php

namespace App\Tests\Web\Navigation;

use App\Entity\Article;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BasicNavigationTest extends WebTestCase
{
    protected ?EntityManagerInterface $manager;
    protected ?KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        self::bootKernel();
        $container = static::getContainer();

        $this->manager = $container->get(EntityManagerInterface::class);
    }

    public function testHome(): void
    {
        $this->client->request('GET', '/');
        $this->assertSelectorTextContains('h1', 'Welcome to ip-global blog');
    }

    public function testArticleDetail(): void
    {
        $articles = $this->manager->getRepository(Article::class)->findBy(['deletedAt' => null]);

        /** @var Article $article */
        $article = $articles[array_rand($articles)];

        $this->client->request('GET', '/article/'.$article->getSlug());

        $this->assertSelectorTextContains('h1', $article->getTitle());
    }

    public function testAuthorDetail(): void
    {
        $authors = $this->manager->getRepository(User::class)->findBy(['deletedAt' => null]);

        /** @var User $author */
        $author = $authors[array_rand($authors)];

        $this->client->request('GET', '/author/'.$author->getSlug());

        $this->assertSelectorTextContains('h1', $author->getName());
    }
}
