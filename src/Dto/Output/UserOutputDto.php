<?php


namespace App\Dto\Output;


use App\Entity\User;

class UserOutputDto
{
    public ?int $id;
    public ?string $email;

    public function entityToDto(User $user): self
    {
        $this->id = $user->getId();
        $this->email = $user->getEmail();

        return $this;
    }

}