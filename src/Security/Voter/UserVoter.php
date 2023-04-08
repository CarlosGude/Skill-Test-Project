<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{
    public const PUT = 'PUT';
    public const POST = 'POST';
    public const DELETE = 'DELETE';

    public function __construct(protected Security $security)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::POST, self::PUT, self::DELETE]) && $subject instanceof User;
    }

    /**
     * @param string $attribute
     * @param User $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var null|User $user */
        $user = $token->getUser();

        if(!$user) {
            return false;
        }

        return match ($attribute) {
            self::POST => true,
            self::PUT => $user === $subject,
            self::DELETE => $this->security->isGranted(User::ROLE_ADMIN) && $subject !== $user,
            default => false,
        };

    }
}
