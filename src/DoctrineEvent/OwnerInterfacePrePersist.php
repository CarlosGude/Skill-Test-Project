<?php

namespace App\DoctrineEvent;

use App\Entity\Interfaces\OwnerInterface;
use App\Entity\User;
use App\Logger\OwnerInterfacePrePersistLogger;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

#[AsDoctrineListener('prePersist'/* , 500, 'default' */)]
class OwnerInterfacePrePersist
{
    public function __construct(
        protected Security $security,
        protected LoggerInterface $logger
    ) {
    }

    public function prePersist(PrePersistEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof OwnerInterface) {
            return;
        }

        if ($entity->getUser()) {
            return;
        }

        $user = $this->security->getUser();

        if (!$user instanceof User) {
            $this->logger->error(OwnerInterfacePrePersistLogger::ERROR_USER_NOT_AUTHORIZED, [
                'entity' => $entity,
            ]);
            throw new UnauthorizedHttpException('Only authorized users can persist this entity');
        }

        $entity->setUser($user);
    }
}
