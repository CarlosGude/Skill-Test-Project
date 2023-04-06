<?php


namespace App\DataTransformer\Input;

use App\Dto\Input\InputInterface;
use App\Entity\AbstractEntity;
use App\Exceptions\DataTransformerException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractInputDataTransformer
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected ValidatorInterface $validator,
        protected SerializerInterface $serializer)
    {
    }

    protected abstract function getClass(): string;
    protected abstract function getInputDto($data): InputInterface;

    protected function find(int $id):? AbstractEntity
    {
        return $this->entityManager->getRepository($this->getClass())->find($id);
    }


    public function post(array $data):array | AbstractEntity
    {
        $input = $this->getInputDto($data);
        $entity = $input->post();
        $violationList = $this->validator->validate($input);

        foreach ($this->validator->validate($entity) as $entityError){
            $violationList->add($entityError);
        }

        if($violationList->count() !== 0 ){
            return $this->getErrors($violationList);
        }

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $entity;
    }

    protected abstract function security(AbstractEntity $entity, UserInterface $user): bool;

    public function delete(int|string $id, UserInterface $user):? AbstractEntity
    {
        $entity = $this->find($id);

        if(!$entity){
            return null;
        }

        if(!$this->security($entity,$user)){
            throw new AccessDeniedHttpException();
        }

        $entity->setDeletedAt();

        $this->entityManager->flush();

        return $entity;
    }

    protected function getErrors(ConstraintViolationListInterface $list): array
    {
        $errors = array();
        /** @var ConstraintViolationInterface $error */
        foreach ($list as $error){
            $errors[$error->getPropertyPath()] = $error->getMessage();

        }

        return $errors;
    }


}