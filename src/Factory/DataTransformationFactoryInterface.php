<?php


namespace App\Factory;

 use App\Entity\AbstractEntity;
 use Symfony\Component\Security\Core\User\UserInterface;

 interface DataTransformationFactoryInterface
{
    public function get(string $entity, int|string $id, string $field = 'id'):? string;
    public function post(string $entity, array $data): string | array;
     public function delete(string $entity, int|string $id, UserInterface $user):? AbstractEntity;
     public function put(string $entity, int|string $id, UserInterface $user, array $body):? string;
}