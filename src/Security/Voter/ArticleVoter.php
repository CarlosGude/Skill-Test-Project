<?php

namespace App\Security\Voter;

use App\Entity\Article;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ArticleVoter extends Voter
{
    public const PUT = 'PUT';
    public const POST = 'POST';
    public const DELETE = 'DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::POST, self::PUT, self::DELETE]) && $subject instanceof Article;
    }

    /**
     * @param string $attribute
     * @param Article $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();


        switch ($attribute) {
            case self::POST:
                return true;
            case self::PUT:
            case self::DELETE:
                return $user instanceof UserInterface && $user === $subject->getUser();
        }

        return false;
    }
}
