<?php

namespace App\Dto\Input;

use App\Entity\AbstractEntity;
use App\Entity\User;
use App\Exceptions\NotExceptedEntityException;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Regex;

class UserDto implements InputInterface
{
    #[Email]
    #[NotBlank]
    #[NotNull]
    public ?string $email;

    #[NotBlank]
    #[NotNull]
    public ?string $name;

    #[Regex('/^(?=.*[a-z])(?=.*\\d).{6,}$/i')]
    #[NotBlank]
    #[NotNull]
    public ?string $password;

    public function __construct(array $data)
    {
        $this->name = $data['name'] ?? null;
        $this->email = $data['email'] ?? null;
        $this->password = $data['password'] ?? null;
    }

    public function initialized(AbstractEntity $entity): void
    {
        if (!$entity instanceof User) {
            throw new NotExceptedEntityException();
        }
        $this->name = $this->name ?? $entity->getName();
        $this->email = $this->email ?? $entity->getEmail();
        $this->password = $this->password ?? $entity->getPassword();
    }

    public function put(AbstractEntity $entity, array $data): AbstractEntity
    {
        if (!$entity instanceof User) {
            throw new NotExceptedEntityException();
        }

        $entity->setName($data['name'] ?? $this->name);
        $entity->setEmail($data['email'] ?? $this->email);
        $entity->setPassword($data['password'] ?? $this->password);

        return $entity;
    }

    public function post(): AbstractEntity
    {
        $user = new User();
        $user->setName($this->name);
        $user->setEmail($this->email);
        $user->setPassword($this->password);

        return $user;
    }
}
