<?php

namespace App\Security\Voter;

use App\Entity\Trick;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class TrickVoter extends Voter
{
    public const EDIT = 'TRICK_EDIT';
    public const DELETE = 'TRICK_DELETE';
    
    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::DELETE])
            && $subject instanceof Trick;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof User) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                // logic to determine if the user can EDIT
                return $this->canEdit($subject, $user);
            case self::DELETE:
                // logic to determine if the user can DELETE
                return $this->canDelete($subject, $user);
        }

        return false;
    }
    
    private function canEdit(Trick $trick, User $user): bool
    {
        return $trick->getAuthor() === $user;
    }

    private function canDelete(Trick $trick, User $user): bool
    {
        return $trick->getAuthor() === $user;
    }
}