<?php

namespace App\Controller;

use App\Factory\DataTransformationFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    public function __construct(protected DataTransformationFactoryInterface $factory)
    {
    }

    /**
     * @throws \Exception
     */
    #[Route('/api/{entity}', name: 'get_entity', methods: ['GET'])]
    #[Route('/api/{entity}/{id}', name: 'get_entity_one', methods: ['GET'])]
    public function get(string $entity, ?string $id): Response
    {
        $data = $this->factory->get($entity, $id);
        $response = new Response($data);
        if (!$data) {
            $response->setStatusCode(404);
        }
        $response->headers->set('Content-Type', 'text/json');

        return $response;
    }

    #[Route('/api/{entity}', name: 'post_entity', methods: ['POST'])]
    public function post(string $entity, RequestStack $request): Response
    {
        /** @var array $body */
        $body = json_decode($request->getMainRequest()->getContent(), true);
        $response = $this->factory->post($entity, $body);

        if (is_array($response)) {
            return $this->json($response['errors'], $response['errorCode']);
        }

        $response = new Response($response, 201);
        $response->headers->set('Content-Type', 'text/json');

        return $response;
    }

    #[Route('/api/{entity}/{id}', name: 'put_entity', methods: ['put'])]
    public function put(RequestStack $request, string $entity, string|int $id): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'User not authorized'], Response::HTTP_FORBIDDEN);
        }

        /** @var array $body */
        $body = json_decode($request->getMainRequest()->getContent(), true);
        $data = $this->factory->put($entity, $id, $body);

        $response = new Response();
        $response->headers->set('Content-Type', 'text/json');

        if (is_array($data) && array_key_exists('errors', $data) && array_key_exists('errorCode', $data)) {
            $response->setContent(json_encode($data['errors']))->setStatusCode($data['errorCode']);

            return $response;
        }

        $response->setContent($data);

        return $response;
    }

    #[Route('/api/{entity}/{id}', name: 'delete_entity', methods: ['DELETE'])]
    public function delete(string $entity, string|int $id): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'User not authorized'], Response::HTTP_FORBIDDEN);
        }

        $data = $this->factory->delete($entity, $id);

        $response = new Response();
        if (is_array($data) && array_key_exists('errors', $data) && array_key_exists('errorCode', $data)) {
            $response->headers->set('Content-Type', 'text/json');
            $response->setContent(json_encode($data['errors']))->setStatusCode($data['errorCode']);

            return $response;
        }

        return new Response(null, $data ? 204 : 404);
    }
}
