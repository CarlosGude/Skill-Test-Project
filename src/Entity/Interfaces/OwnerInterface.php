<?php


namespace App\Entity\Interfaces;


use Symfony\Component\Security\Core\User\UserInterface;

interface OwnerInterface
{
    public function setUser(UserInterface $user): self;
    public function getUser():? UserInterface;
}