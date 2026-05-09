<?php

// src/Security/Voter/CommentVoter.php

namespace App\Security\Voter;

use App\Entity\Comment;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class CommentVoter extends Voter
{
    public const EDIT = 'COMMENT_EDIT';
    public const DELETE = 'COMMENT_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // Check if the attribute is supported and if the subject is a Comment entity
        return in_array($attribute, [self::EDIT, self::DELETE], true)
            && $subject instanceof Comment;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var User|null $user */
        $user = $token->getUser();

        // If the user is not logged in or is not our User entity, deny access
        if (!$user instanceof User) {
            return false;
        }

        /** @var Comment $comment */
        $comment = $subject; // Explicitly tell PHP that $subject is a Comment

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($comment, $user);

            case self::DELETE:
                return $this->canDelete($comment, $user);
        }

        return false;
    }

    private function canEdit(Comment $comment, User $user): bool
    {
        return $comment->getAuthor()?->getId() === $user->getId();
    }

    private function canDelete(Comment $comment, User $user): bool
    {
        return $comment->getAuthor()?->getId() === $user->getId();
    }
}
