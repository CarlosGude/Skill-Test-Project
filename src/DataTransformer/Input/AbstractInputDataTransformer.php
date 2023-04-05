<?php


namespace App\DataTransformer\Input;

use App\Dto\Input\InputInterface;
use App\Entity\AbstractEntity;
use Doctrine\ORM\EntityManagerInterface;

use Exception;
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

    /**
     * @throws Exception
     */
    public function post(array $data):array | AbstractEntity
    {
        $input = $this->getInputDto($data);

        try {
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

        }catch (Exception $exception){
            throw $exception;//TODO Custom exception
        }

        return $entity;
    }

    public function delete(int|string $id):? AbstractEntity
    {
        $entity = $this->find($id);

        if(!$entity){
            return null;
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