<?php


namespace App\DataTransformer\Input;

use App\Dto\Input\InputInterface;
use App\Dto\Input\UserInputDto;
use App\Entity\AbstractEntity;
use App\Entity\User;
use App\Exceptions\NotExceptedEntityException;
use Symfony\Component\Security\Core\User\UserInterface;

class UserInputDataTransformer extends AbstractInputDataTransformer
{

    protected function getClass(): string
    {
        return User::class;
    }

    protected function getInputDto($data): InputInterface
    {
        return new UserInputDto($data);
    }

    /**
     * @throws NotExceptedEntityException
     */
    protected function security(AbstractEntity $entity, UserInterface $user): bool
    {
        if(!$entity instanceof User){
            throw new  NotExceptedEntityException();
        }
        return $entity->getId() === $user->getId();
    }
}