<?php
// src/Security/Voter/VideoVoter.php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\Video;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class VideoVoter extends Voter
{
public const DELETE = 'VIDEO_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::DELETE])
            && $subject instanceof Video;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof User) {
            return false;
        }

        if (!$subject instanceof Video) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        return match ($attribute) {
            self::DELETE => $this->canDelete($subject, $user),
            default => false
        };

    }
    
    private function canDelete(Video $video, User $user): bool
    {
        $trick = $video->getTrick();

        if (!$trick) {
            return false;
        }

        // ownership
        if ($trick->getAuthor() !== $user) {
            return false;
        }

        // At lest one image in trick so not delete if only one image in the trick
        if ($trick->getVideos()->count() <= 1) {
            return false;
        }

        return true;
    }
}