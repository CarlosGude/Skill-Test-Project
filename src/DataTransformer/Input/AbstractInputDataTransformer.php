<?php

namespace App\DataTransformer\Input;

use App\Dto\Input\InputInterface;
use App\Entity\AbstractEntity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractInputDataTransformer
{
    public function __construct(
        protected AuthorizationCheckerInterface $voter,
        protected EntityManagerInterface $entityManager,
        protected ValidatorInterface $validator,
        protected SerializerInterface $serializer
    ) {
    }

    abstract protected function getClass(): string;
    abstract protected function getInputDto(array $data): InputInterface;

    protected function find(string $id): ?AbstractEntity
    {
        return $this->entityManager->getRepository($this->getClass())->findOneBy(['uuid' => $id,'deletedAt' => null]);
    }


    public function post(array $data): array | AbstractEntity
    {
        $input = $this->getInputDto($data);
        $entity = $input->post();

        if(!$this->voter->isGranted('POST', $entity)) {
            throw new AccessDeniedException();
        }

        $violationList = $this->validator->validate($input);

        foreach ($this->validator->validate($entity) as $entityError) {
            $violationList->add($entityError);
        }

        if($violationList->count() !== 0) {
            return $this->getErrors($violationList);
        }

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $entity;
    }

    public function put(string $id, array $data): array | AbstractEntity
    {
        $input = $this->getInputDto($data);
        $entity = $this->find($id);

        if(!$entity) {
            throw new NotFoundHttpException();
        }

        $input->initialized($entity);
        $entity = $input->put($entity, $data);

        if(!$this->voter->isGranted('PUT', $entity)) {
            throw new AccessDeniedException();
        }

        $violationList = $this->validator->validate($input);

        foreach ($this->validator->validate($entity) as $entityError) {
            $violationList->add($entityError);
        }

        if($violationList->count() !== 0) {
            return $this->getErrors($violationList);
        }

        $this->entityManager->flush();

        return $entity;
    }

    public function delete(int|string $id): AbstractEntity
    {
        $entity = $this->find($id);

        if(!$entity) {
            throw new NotFoundHttpException();
        }

        if(!$this->voter->isGranted('DELETE', $entity)) {
            throw new AccessDeniedException();
        }

        $entity->setDeletedAt();

        $this->entityManager->flush();

        return $entity;
    }

    protected function getErrors(ConstraintViolationListInterface $list): array
    {
        $errors = array();
        /** @var ConstraintViolationInterface $error */
        foreach ($list as $error) {
            $errors[$error->getPropertyPath()] = $error->getMessage();

        }

        return $errors;
    }


}
