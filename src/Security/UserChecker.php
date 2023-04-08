<?php

namespace App\Security;

use App\Entity\User;
use App\Logger\UserCheckerLogger;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function __construct(protected LoggerInterface $logger)
    {
    }

    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        if (!$user->isActive()) {
            $this->logger->error(UserCheckerLogger::ERROR_USER_NOT_ACTIVE, [
                'user' => $user,
            ]);
            throw new AccessDeniedHttpException();
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        if ($user->getDeletedAt()) {
            $this->logger->error(UserCheckerLogger::ERROR_USER_DELETED, [
                'user' => $user,
            ]);
            throw new AccessDeniedHttpException();
        }
    }
}
