<?php

namespace App\DataTransformer\Input;

use App\Dto\Input\InputInterface;
use App\Entity\AbstractEntity;
use App\Logger\DataTransformationLogger;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
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
        protected Security $security,
        protected AuthorizationCheckerInterface $voter,
        protected EntityManagerInterface $entityManager,
        protected ValidatorInterface $validator,
        protected SerializerInterface $serializer,
        protected LoggerInterface $logger
    ) {
    }

    abstract protected function getClass(): string;

    abstract protected function getInputDto(array $data): InputInterface;

    protected function find(string $id): ?AbstractEntity
    {
        return $this->entityManager->getRepository($this->getClass())->findOneBy(['uuid' => $id, 'deletedAt' => null]);
    }

    public function post(array $data): array|AbstractEntity
    {
        $input = $this->getInputDto($data);
        $entity = $input->post();

        if (!$this->voter->isGranted('POST', $entity)) {
            $this->logger->error(DataTransformationLogger::ERROR_ACCESS_DENIED_EXCEPTION, [
                'method' => 'POST',
                'data' => $entity,
                'class' => $this->getClass(),
                'user' => $this->security->getUser(),
            ]);
            return ['errorCode' => Response::HTTP_UNAUTHORIZED, 'errors' => []];
        }

        if (!empty($errors = $this->validateData($input, $entity))) {
            return ['errorCode' => Response::HTTP_BAD_REQUEST, 'errors' => $errors];
        }

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $entity;
    }

    public function put(string $id, array $data): array|AbstractEntity
    {
        $input = $this->getInputDto($data);
        $entity = $this->find($id);

        if (!$entity) {
            $this->logger->error(DataTransformationLogger::ERROR_ENTITY_NOT_FOUND, [
                'class' => $this->getClass(),
                'id' => $id,
            ]);
            return ['errorCode' => Response::HTTP_NOT_FOUND, 'errors' => ['message' => 'Data not found']];
        }

        $input->initialized($entity);
        $entity = $input->put($entity, $data);

        if (!$this->voter->isGranted('PUT', $entity)) {
            $this->logger->error(DataTransformationLogger::ERROR_ACCESS_DENIED_EXCEPTION, [
                'method' => 'PUT',
                'class' => $this->getClass(),
                'id' => $id,
                'data' => $entity,
                'user' => $this->security->getUser(),
            ]);
            return ['errorCode' => Response::HTTP_UNAUTHORIZED, 'errors' => ['message' => 'User not authorized for this action']];
        }

        if (!empty($errors = $this->validateData($input, $entity))) {
            return ['errorCode' => Response::HTTP_BAD_REQUEST, 'errors' => $errors];
        }

        $this->entityManager->flush();

        return $entity;
    }

    public function delete(int|string $id): array|AbstractEntity
    {
        $entity = $this->find($id);

        if (!$entity) {
            $this->logger->error(DataTransformationLogger::ERROR_ENTITY_NOT_FOUND, [
                'class' => $this->getClass(),
                'id' => $id,
            ]);
            return ['errorCode' => Response::HTTP_NOT_FOUND, 'errors' => ['message' => 'Data not found']];
        }

        if (!$this->voter->isGranted('DELETE', $entity)) {
            $this->logger->error(DataTransformationLogger::ERROR_ACCESS_DENIED_EXCEPTION, [
                'method' => 'DELETE',
                'id' => $id,
                'data' => $entity,
                'user' => $this->security->getUser(),
            ]);
            return ['errorCode' => Response::HTTP_UNAUTHORIZED, 'errors' => ['message' => 'User not authorized for this action']];
        }

        $entity->setDeletedAt();

        $this->entityManager->flush();

        return $entity;
    }

    protected function getErrors(ConstraintViolationListInterface $list): array
    {
        $errors = [];
        /** @var ConstraintViolationInterface $error */
        foreach ($list as $error) {
            $errors[$error->getPropertyPath()] = $error->getMessage();
        }

        return $errors;
    }

    protected function validateData(InputInterface $input, AbstractEntity $entity): array
    {
        $violationList = $this->validator->validate($input);

        foreach ($this->validator->validate($entity) as $entityError) {
            $violationList->add($entityError);
        }

        if (0 !== $violationList->count()) {
            $this->logger->warning(DataTransformationLogger::ERROR_IN_DATA_VALIDATION, [
                'method' => 'POST',
                'class' => $this->getClass(),
                'data' => $entity,
                'violationList' => $violationList,
            ]);

            return $this->getErrors($violationList);
        }

        return [];
    }
}
