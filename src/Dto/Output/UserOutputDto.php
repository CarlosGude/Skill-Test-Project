<?php

namespace App\Dto\Output;

use App\Entity\AbstractEntity;
use App\Entity\Article;
use App\Entity\User;
use App\Exceptions\EntityOutputException;

class UserOutputDto implements OutputInterface
{
    public ?string $uuid;
    public ?string $email;
    public ?string $name;
    public ?string $slug;
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
        if(!$entity instanceof User) {
            throw new EntityOutputException();
        }

        $this->uuid = $entity->getUuid();
        $this->name = $entity->getName();
        $this->slug = $entity->getSlug();
        $this->email = $entity->getEmail();

        if($this->getNestedElements) {
            /** @var Article $article */
            foreach ($entity->getArticles() as $article) {
                if(is_null($article->getDeletedAt())) {
                    $this->articles[] = (new ArticleOutputDto(false))->get($article);
                }
            }
        }

        return $this;
    }

}
