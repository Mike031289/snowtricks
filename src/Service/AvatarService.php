<?php

// src/Service/AvatarService.php

/*
 * This file is part of the SnowTricks project.
 *
 * (c) Adjoukou AGBELOU <adjoukouagbelou@gmail.com> Dev-Application PHP Symfony
*/

namespace App\Service;

use App\Entity\User;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class AvatarService
{
    public function __construct(
        #[Autowire('%avatars_directory%')]
        private string $uploadDirectory,
        private SluggerInterface $slugger,
    ) {
    }

    public function upload(User $user, UploadedFile $file): string
    {
        // 1. Get username and ensure it's a string (fallback to User ID or 'user' if null)
        $username = $user->getUsername() ?? 'user-'.$user->getId();

        // 2. Generate slug safely
        $slug = $this->slugger->slug((string) $username)->lower();

        // 3. Secure the extension
        $extension = $file->guessExtension() ?: 'jpg';

        // 4. Create final filename
        $filename = 'avatar_'.$slug.'_'.uniqid().'.'.$extension;

        // 5. Move the file
        $file->move($this->uploadDirectory, $filename);

        return $filename;
    }
}
