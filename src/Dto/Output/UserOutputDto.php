<?php


namespace App\Dto\Output;


use App\Entity\AbstractEntity;
use App\Entity\User;
use App\Exceptions\EntityOutputException;


class UserOutputDto implements OutputInterface
{
    public ?int $id;
    public ?string $uuid;
    public ?string $email;
    public ?string $name;

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

        return $this;
    }

}