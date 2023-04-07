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
    public function __construct(protected EntityManagerInterface $manager){}

    #[Route('/', name: 'app_base')]
    public function index(): Response
    {

        // TODO: Pendiente de crear la vista
        return $this->render('base/index.html.twig', [
            'controller_name' => 'BaseController',
            'articles' => $this->manager->getRepository(Article::class)->findAll()
        ]);
    }

    #[Route('/article/{id}', name: 'article_detail')]
    public function getArticle(int $id): Response
    {
        $article = $this->manager->getRepository(Article::class)->findOneBy(['id' => $id, 'deletedAt' => null]);
        if(!$article){
            throw new NotFoundHttpException();
        }

        // TODO: Pendiente de crear la vista. la url debe de ser el slug del titulo.
        return $this->render('base/index.html.twig', [
            'controller_name' => 'BaseController',
            'article' => $article
        ]);
    }

    #[Route('/author/{id}', name: 'author_detail')]
    public function getAuthor(int $id): Response
    {
        $author = $this->manager->getRepository(User::class)->findOneBy(['id' => $id, 'deletedAt' => null]);
        if(!$author){
            throw new NotFoundHttpException();
        }

        // TODO: Pendiente de crear la vista. la url debe de ser el slug del nombre del autor.
        return $this->render('base/index.html.twig', [
            'controller_name' => 'BaseController',
            'author' => $author
        ]);
    }
}
