<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class BaseController extends AbstractController
{
    public function __construct(protected EntityManagerInterface $manager)
    {
    }

    #[Route('/', name: 'app_base')]
    public function index(): Response
    {
        return $this->render('base/index.html.twig', [
            'controller_name' => 'BaseController',
            'articles' => $this->manager->getRepository(Article::class)->findAll(),
        ]);
    }

    #[Route('/article/{slug}', name: 'article_detail')]
    public function getArticle(string $slug): Response
    {
        $article = $this->manager->getRepository(Article::class)->findOneBy(['slug' => $slug, 'deletedAt' => null]);
        if (!$article) {
            throw new NotFoundHttpException();
        }

        return $this->render('base/article.html.twig', [
            'controller_name' => 'BaseController',
            'article' => $article,
        ]);
    }

    #[Route('/author/{slug}', name: 'author_detail')]
    public function getAuthor(string $slug): Response
    {
        $author = $this->manager->getRepository(User::class)->findOneBy(['slug' => $slug, 'deletedAt' => null]);
        if (!$author) {
            throw new NotFoundHttpException();
        }

        return $this->render('base/author.html.twig', [
            'controller_name' => 'BaseController',
            'author' => $author,
        ]);
    }
}
