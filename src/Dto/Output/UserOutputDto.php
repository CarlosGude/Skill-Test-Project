<?php


namespace App\Dto\Output;


use App\Entity\AbstractEntity;
use App\Entity\Article;
use App\Entity\User;
use App\Exceptions\EntityOutputException;


class UserOutputDto implements OutputInterface
{
    public ?int $id;
    public ?string $uuid;
    public ?string $email;
    public ?string $name;
    public array $articles = [];

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
        if(!$entity instanceof User){
            throw new EntityOutputException();
        }

        $this->id = $entity->getId();
        $this->uuid = $entity->getUuid();
        $this->name = $entity->getName();
        $this->email = $entity->getEmail();

        if($this->getNestedElements){
            /** @var Article $article */
            foreach ($entity->getArticles() as $article){
                if(is_null($article->getDeletedAt())){
                    $this->articles[] = (new ArticleOutputDto(false))->get($article);
                }
            }
        }

        return $this;
    }

}