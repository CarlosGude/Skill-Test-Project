<?php

namespace App\Entity\Interfaces;

use App\Entity\User;

interface OwnerInterface
{
    public function setUser(User $user): self;

    public function getUser(): ?User;
}
