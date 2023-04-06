<?php


namespace App\DataTransformer\Input;

use App\Dto\Input\ArticleInputDto;
use App\Dto\Input\InputInterface;
use App\Entity\AbstractEntity;
use App\Entity\Article;
use App\Exceptions\NotExceptedEntityException;
use Symfony\Component\Security\Core\User\UserInterface;

class ArticleInputDataTransformer extends AbstractInputDataTransformer
{

    protected function getClass(): string
    {
        return Article::class;
    }

    protected function getInputDto($data): InputInterface
    {
        return new ArticleInputDto($data);
    }

    /**
     * @throws NotExceptedEntityException
     */
    protected function security(AbstractEntity $entity, UserInterface $user): bool
    {
        if(!$entity instanceof Article){
            throw new  NotExceptedEntityException();
        }
        return $entity->getUser() === $user;
    }
}