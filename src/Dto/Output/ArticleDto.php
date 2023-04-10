<?php

namespace App\Dto\Output;

use App\Entity\AbstractEntity;
use App\Entity\Article;
use App\Exceptions\EntityOutputException;

class ArticleDto implements OutputInterface
{
    public ?string $uuid;
    public ?string $title;
    public ?string $slug;
    public ?string $body;
    public ?UserDto $author;

    public function __construct(protected bool $getNestedElements = true)
    {
    }

    /**
     * @return $this
     *
     * @throws EntityOutputException
     */
    public function get(AbstractEntity $entity): self
    {
        if (!$entity instanceof Article) {
            throw new EntityOutputException();
        }

        $this->uuid = $entity->getUuid();
        $this->title = $entity->getTitle();
        $this->slug = $entity->getSlug();
        $this->body = $entity->getBody();

        if ($this->getNestedElements) {
            $this->author = (new UserDto())->get($entity->getUser());
        }

        return $this;
    }
}
