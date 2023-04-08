<?php


namespace App\MessageHandler;

use App\Email\AbstractEmail;
use App\Entity\User;
use App\Message\EntityEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UserCreateMessageHandler
{
    public function __construct(protected EntityManagerInterface $manager, protected AbstractEmail $activateUserEmail){}

    public function __invoke(EntityEvent $message): void
    {
        if($message->getClass() !== User::class){
            return;
        }

        if($message->getEvent() !== EntityEvent::EVENT_CREATE){
            return;
        }

        /** @var null|User $user */
        $user = $this->manager->getRepository(User::class)->findOneBy(['uuid' => $message->uuid]);

        if(!$user){
            return;
        }

        $this->activateUserEmail->prepareEmail($user)->send();
    }
}