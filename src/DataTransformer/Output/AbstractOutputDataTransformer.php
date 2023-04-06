<?php


namespace App\DataTransformer\Output;

use App\Dto\Output\OutputInterface;
use App\Entity\AbstractEntity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;

abstract class AbstractOutputDataTransformer
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected SerializerInterface $serializer)
    {
    }

    protected abstract function getClass();
    protected abstract function getOutputDto();

    public function get( ?string $uuid, ?string $field):? string
    {
        $data = ($uuid) ? $this->getItem($field,$uuid) : $this->getAllItems();

        if(empty($data)){
            return null;
        }

        return $this->serializer->serialize($this->entityToDto($data),'json');
    }

    private function entityToDto(array|AbstractEntity $data): array|OutputInterface
    {
        if(is_array($data)){
            $response = array();
            foreach ($data as $item){
                /** @var OutputInterface $dto */
                $dto = $this->getOutputDto();
                $response[] = $dto->get($item);
            }
            return $response;
        }

        $dto = $this->getOutputDto();

        return $dto->get($data);
    }

    protected function getAllItems(): array
    {
        return $this->entityManager->getRepository($this->getClass())->findAll(['deletedAt' => null]);
    }

    protected function getItem( string $field, int|string $id)
    {
        return $this->entityManager->getRepository($this->getClass())->findOneBy([$field => $id,'deletedAt' => null]);
    }
}