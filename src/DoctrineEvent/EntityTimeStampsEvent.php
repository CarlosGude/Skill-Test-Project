<?php

namespace App\DoctrineEvent;

use App\Entity\AbstractEntity;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Bundle\SecurityBundle\Security;

#[AsDoctrineListener('prePersist'/*, 500, 'default'*/)]
#[AsDoctrineListener('preUpdate'/*, 500, 'default'*/)]
class EntityTimeStampsEvent
{
    public function __construct(protected Security $security)
    {
    }

    public function prePersist(PrePersistEventArgs $args): void
    {
        $entity = $args->getObject();

        if(!$entity instanceof AbstractEntity) {
            return;
        }

        $entity->setCreatedAt();
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();

        if(!$entity instanceof AbstractEntity) {
            return;
        }

        $entity->setUpdatedAt();
    }

}
