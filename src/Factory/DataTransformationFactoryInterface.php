<?php


namespace App\Factory;

 interface DataTransformationFactoryInterface
{
    public function transformation(string $entity, ?string $field, string|int|null $id):? string;
}