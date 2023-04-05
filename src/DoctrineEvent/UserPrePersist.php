<?php


namespace App\DoctrineEvent;


use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsDoctrineListener('prePersist'/*, 500, 'default'*/)]
class UserPrePersist
{
    public function __construct(protected UserPasswordHasherInterface $passwordHasher){}
    public function prePersist(PrePersistEventArgs $args): void
    {
        $entity = $args->getObject();

        if(!$entity instanceof User){
            return;
        }

        $entity->setPassword($this->passwordHasher->hashPassword($entity,$entity->getPassword()));
    }
}