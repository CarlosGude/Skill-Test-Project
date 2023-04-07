<?php


namespace App\Factory;

 use App\Entity\AbstractEntity;

 interface DataTransformationFactoryInterface
{
    public function get(string $entity, int|string $id, string $field = 'id'):? string;
    public function post(string $entity, array $data): string | array;
     public function delete(string $entity, int|string $id):? AbstractEntity;
     public function put(string $entity, int|string $id, array $body):? string;
}