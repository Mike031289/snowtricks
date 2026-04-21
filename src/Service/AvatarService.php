<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class AvatarService
{
    public function __construct(
        #[Autowire('%avatars_directory%')]
        private string $avatarsDirectory
    ) {}

    public function upload(User $user, UploadedFile $file): string
    {
        // delete old avatar
        if ($user->getAvatar()) {
            $oldPath = $this->avatarsDirectory . '/' . $user->getAvatar();

            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        // generate filename
        $filename = uniqid('avatar_') . '.' . $file->guessExtension();

        // move file
        $file->move($this->avatarsDirectory, $filename);

        return $filename;
    }
}