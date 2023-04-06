<?php


namespace App\Dto\Output;


use App\Entity\AbstractEntity;
use App\Entity\Article;
use App\Exceptions\EntityOutputException;


class ArticleOutputDto implements OutputInterface
{
    public ?int $id;
    public ?string $uuid;
    public ?string $title;
    public ?string $body;
    public ?UserOutputDto $author;

    public function __construct(protected bool $getNestedElements = true)
    {
    }

    /**
     * @param AbstractEntity $entity
     * @return $this
     * @throws EntityOutputException
     */
    public function get(AbstractEntity $entity): self
    {
        if(!$entity instanceof Article){
            throw new EntityOutputException();
        }

        $this->id = $entity->getId();
        $this->uuid = $entity->getUuid();
        $this->title = $entity->getTitle();
        $this->body = $entity->getBody();

        if($this->getNestedElements){
            $this->author = (new UserOutputDto())->get($entity->getUser());
        }

        return $this;
    }

}