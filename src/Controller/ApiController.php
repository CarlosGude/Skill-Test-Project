<?php

namespace App\Controller;

use App\Factory\DataTransformationFactoryInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    public function __construct(protected DataTransformationFactoryInterface $factory){}

    /**
     * @throws Exception
     */
    #[Route('/api/{entity}', name: 'get_entity',methods: ['GET'])]
    #[Route('/api/{entity}/{id}/{field}', name: 'get_entity_one',methods: ['GET'])]
    public function index(string $entity, ?string $id, ?string $field = 'id'): JsonResponse
    {
        return $this->json([
            'data' => $this->factory->transformation($entity,$id,$field)
        ]);
    }
}
