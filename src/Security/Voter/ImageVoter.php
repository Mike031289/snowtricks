<?php

namespace App\Security\Voter;

use App\Entity\Image;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class ImageVoter extends Voter
{
    public const DELETE = 'IMAGE_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // Return true if the attribute is one we support && if the subject on witch we vote is an instance of the object we wants to access
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::DELETE])
            && $subject instanceof Image;

    }


    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();
        
        // if the user is anonymous, do not grant acces : he must be logged in
        if (!$user instanceof User) {
            return false;
        }

        if (!$subject instanceof Image) {
            return false;
        }
        if ($subject->isMain()) {
            throw new \Exception('Impossible de supprimer l’image principale');
        }

        // ... (check conditions and return true to grant permission) ...
        return match ($attribute) {
            self::DELETE => $this->canDelete($subject, $user),
            default => false
        };

    }

    private function canDelete(Image $image, User $user): bool
    {
        $trick = $image->getTrick();

        if (!$trick) {
            return false;
        }

        // ownership
        if ($trick->getAuthor() !== $user) {
            return false;
        }

        // At lest one image in trick so not delete if only one image in the trick
        if ($trick->getImages()->count() <= 1 || ($trick->getImages() === ($trick->getMainImage()))){

            return false;
        }

        return true;
    }
}