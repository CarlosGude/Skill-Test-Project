<?php

namespace App\DoctrineEvent;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsDoctrineListener('prePersist'/* , 500, 'default' */)]
#[AsDoctrineListener('preUpdate'/* , 500, 'default' */)]
class UserPasswordEvent
{
    public function __construct(protected UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function prePersist(PrePersistEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof User) {
            return;
        }

        $entity->setPassword($this->passwordHasher->hashPassword($entity, $entity->getPassword()));
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof User) {
            return;
        }

        if (!$args->hasChangedField('password')) {
            return;
        }

        $entity->setPassword($this->passwordHasher->hashPassword($entity, $entity->getPassword()));
    }
}
