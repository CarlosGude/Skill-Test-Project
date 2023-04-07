<?php


namespace App\DoctrineEvent;


use App\Entity\AbstractEntity;
use App\Message\EntityEvent;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\UnitOfWork;
use Symfony\Component\Messenger\MessageBusInterface;


#[AsDoctrineListener('onFlush'/*, 500, 'default'*/)]
#[AsDoctrineListener('postFlush'/*, 500, 'default'*/)]
class EntityEventPosFlush
{
    protected array $events = array();

    public function __construct(protected MessageBusInterface $bus){}

    public function onFlush(OnFlushEventArgs $args): void
    {
        $em = $args->getObjectManager();
        /** @var UnitOfWork $uow */
        $uow = $em->getUnitOfWork();

        /** @var AbstractEntity $insertion */
        foreach ($uow->getScheduledEntityInsertions() as $insertion){
            $this->events[] = new EntityEvent(
                $insertion::class,
                EntityEvent::EVENT_CREATE,
                $insertion->getUuid(),
            );
        }

        /** @var AbstractEntity $insertion */
        foreach ($uow->getScheduledEntityUpdates() as $insertion){
            $this->events[] = new EntityEvent(
                $insertion::class,
                EntityEvent::EVENT_UPDATE,
                $insertion->getUuid(),
                $uow->getEntityChangeSet($insertion)
            );
        }
    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        /** @var EntityEvent $event */
        foreach ($this->events as $event){
            $this->bus->dispatch($event);
        }
    }
}