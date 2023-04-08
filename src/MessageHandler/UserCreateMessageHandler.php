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
    public function __construct(protected EntityManagerInterface $manager, protected AbstractEmail $activateUserEmail)
    {
    }

    public function __invoke(EntityEvent $message): void
    {
        if (User::class !== $message->getClass()) {
            return;
        }

        if (EntityEvent::EVENT_CREATE !== $message->getEvent()) {
            return;
        }

        /** @var User|null $user */
        $user = $this->manager->getRepository(User::class)->findOneBy(['uuid' => $message->uuid]);

        if (!$user) {
            return;
        }

        $this->activateUserEmail->prepareEmail($user)->send();
    }
}
