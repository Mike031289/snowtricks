<?php

// src/Security/Voter/TrickVoter.php

namespace App\Security\Voter;

use App\Entity\Trick;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class TrickVoter extends Voter
{
    public const ADD = 'TRICK_ADD';
    public const EDIT = 'TRICK_EDIT';
    public const DELETE = 'TRICK_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // Supports ADD, EDIT, DELETE only if the subject is an instance of Trick
        return in_array($attribute, [self::ADD, self::EDIT, self::DELETE], true)
            && $subject instanceof Trick;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var User|null $user */
        $user = $token->getUser();

        // If the user is anonymous, deny access
        if (!$user instanceof User) {
            return false;
        }

        /** @var Trick $trick */
        $trick = $subject; // Explicitly casting $subject to Trick for type safety

        switch ($attribute) {
            case self::ADD:
                // Call specific logic for adding
                return $this->canAdd($trick, $user);

            case self::EDIT:
                // Call specific logic for editing
                return $this->canEdit($trick, $user);

            case self::DELETE:
                // Call specific logic for deleting
                return $this->canDelete($trick, $user);
        }

        return false;
    }

    private function canAdd(Trick $trick, User $user): bool
    {
        return $trick->getAuthor()?->getId() === $user->getId();
    }

    private function canEdit(Trick $trick, User $user): bool
    {
        return $trick->getAuthor()?->getId() === $user->getId();
    }

    private function canDelete(Trick $trick, User $user): bool
    {
        return $trick->getAuthor()?->getId() === $user->getId();
    }
}
