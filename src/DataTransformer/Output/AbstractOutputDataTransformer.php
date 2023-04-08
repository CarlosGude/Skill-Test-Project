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
        protected SerializerInterface $serializer
    ) {
    }

    abstract protected function getClass(): string;

    abstract protected function getOutputDto(): OutputInterface;

    public function get(?string $uuid): ?string
    {
        $data = ($uuid) ? $this->getItem($uuid) : $this->getAllItems();

        if (empty($data)) {
            return null;
        }

        return $this->serializer->serialize($this->entityToDto($data), 'json');
    }

    private function entityToDto(array|AbstractEntity $data): array|OutputInterface
    {
        if (is_array($data)) {
            $response = [];
            foreach ($data as $item) {
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
        return $this->entityManager->getRepository($this->getClass())->findBy(['deletedAt' => null]);
    }

    protected function getItem(string $id): ?AbstractEntity
    {
        return $this->entityManager->getRepository($this->getClass())->findOneBy(['uuid' => $id, 'deletedAt' => null]);
    }
}
