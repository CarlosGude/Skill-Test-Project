<?php


namespace App\Dto\Input;


use App\Entity\AbstractEntity;
use App\Entity\Article;
use App\Exceptions\NotExceptedEntityException;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;


class ArticleInputDto implements InputInterface
{
    #[NotBlank]
    #[NotNull]
    public ?string $title;

    #[NotBlank]
    #[NotNull]
    public ?string $body;

    public function __construct(array $data)
    {
        $this->title = $data['title'] ?? null;
        $this->body = $data['body'] ?? null;
    }

    public function initialized(AbstractEntity $entity): void
    {
        if(!$entity instanceof Article){
            throw new NotExceptedEntityException();
        }
        $this->title = $this->title ?? $entity->getTitle();
        $this->body = $this->body ?? $entity->getBody();
    }

    public function put(AbstractEntity $entity,array $data): AbstractEntity
    {
        if(!$entity instanceof Article){
            throw new NotExceptedEntityException();
        }

        $entity->setTitle($data['title'] ?? $this->title);
        $entity->setBody($data['body'] ?? $this->body);

        return $entity;
    }

    public function post(): AbstractEntity
    {
        $article = new Article();
        $article->setTitle($this->title);
        $article->setBody($this->body);

        return $article;
    }
}