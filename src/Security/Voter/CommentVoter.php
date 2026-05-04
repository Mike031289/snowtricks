<?php

// src/Security/Voter/CommentVoter.php

namespace App\Security\Voter;

use App\Entity\Comment;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class CommentVoter extends Voter
{
    public const EDIT = 'COMMENT_EDIT';
    public const DELETE = 'COMMENT_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::DELETE])
            && $subject instanceof Comment;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                // logic to determine if the user can EDIT
                return $this->canEdit($subject, $user);
                // return true or false
                break;
            case self::DELETE:
                // logic to determine if the user can DELETE
                return $this->canDelete($subject, $user);
                // return true or false
                break;
        }

        return false;
    }

    private function canEdit(Comment $trick, User $user): bool
    {
        return $trick->getAuthor() === $user;
    }

    private function canDelete(Comment $trick, User $user): bool
    {
        return $trick->getAuthor() === $user;
    }
}
