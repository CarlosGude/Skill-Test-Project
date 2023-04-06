<?php

namespace App\Controller;

use App\Factory\DataTransformationFactoryInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    public function __construct(protected DataTransformationFactoryInterface $factory){}

    /**
     * @throws Exception
     */
    #[Route('/api/{entity}', name: 'get_entity',methods: ['GET'])]
    #[Route('/api/{entity}/{id}/{field}', name: 'get_entity_one',methods: ['GET'])]
    public function get(string $entity, ?string $id, ?string $field = 'id'): Response
    {
        $data = $this->factory->get($entity,$id,$field);
        $response = new Response($data);
        if(!$data){
            $response->setStatusCode(404);
        }
        $response->headers->set('Content-Type', 'text/json');

        return $response;
    }

    #[Route('/api/{entity}', name: 'post_entity',methods: ['POST'])]
    public function post(string $entity, RequestStack $request): Response
    {
        $response = $this->factory->post($entity,json_decode($request->getMainRequest()->getContent(),true));

        if(is_array($response)){
            return $this->json($response,400);
        }

        $response = new Response($response,201);
        $response->headers->set('Content-Type', 'text/json');
        return $response;

    }

    #[Route('/api/{entity}/{id}', name: 'delete_entity',methods: ['DELETE'])]
    public function delete(string $entity, string|int $id): Response
    {
        $user = $this->getUser();
        if(!$user){
            throw new AccessDeniedHttpException();
        }

        $response = $this->factory->delete($entity,$id, $user);

        return new Response(null,$response? 204:404);

    }
}
