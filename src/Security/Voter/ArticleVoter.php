<?php

namespace App\Security\Voter;

use App\Entity\Article;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ArticleVoter extends Voter
{
    public const PUT = 'PUT';
    public const POST = 'POST';
    public const DELETE = 'DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::POST, self::PUT, self::DELETE], true) && $subject instanceof Article;
    }

    /**
     * @param Article $subject
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        return match ($attribute) {
            self::POST => true,
            default => $user instanceof User && $user->getId() === $subject->getUser()->getId(),
        };
    }
}
