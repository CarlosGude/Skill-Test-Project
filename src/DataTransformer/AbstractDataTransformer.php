<?php


namespace App\DataTransformer;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Serializer\SerializerInterface;

abstract class AbstractDataTransformer
{
    public const METHOD_GET = 'GET';
    public const METHOD_POST = 'POST';
    public const METHOD_PUT = 'PUT';
    public const METHOD_DELETE = 'DELETE';

    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected SerializerInterface $serializer)
    {
    }

    protected abstract function getClass();
    protected abstract function getOutputDto();

    public function transformation(string $method, ?string $uuid, ?string $field): string
    {

        if($method === self::METHOD_GET){
            $data = ($uuid) ? $this->getItem($field,$uuid) : $this->getAllItems();

            if(is_array($data)){
                $response = array();
                foreach ($data as $item){
                    $dto = $this->getOutputDto();
                    $response[] = $dto->entityToDto($item);
                }
            }else{
                $dto = $this->getOutputDto();
                $response = $dto->entityToDto($data);
            }

            return $this->serializer->serialize($response,'json');
        }

        throw new Exception();
    }

    protected function getAllItems(): array
    {
        return $this->entityManager->getRepository($this->getClass())->findAll();
    }

    protected function getItem( string $field, int|string $id)
    {
        return $this->entityManager->getRepository($this->getClass())->findOneBy([$field => $id]);
    }
}