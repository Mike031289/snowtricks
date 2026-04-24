<?php
// src/Service/AvatarService.php

/*
 * This file is part of the SnowTricks project.
 *
 * (c) Adjoukou AGBELOU <adjoukouagbelou@gmail.com> Dev-Application PHP Symfony
*/

namespace App\Service;

use App\Entity\User;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class AvatarService
{
    public function __construct(
        #[Autowire('%images_directory%')]
        private string $uploadDirectory,

        private SluggerInterface $slugger
    ) {}

    public function upload(User $user, UploadedFile $file): string
    {
        // User slug for filename
        $slug = $this->slugger->slug($user->getUsername())->lower();

        // extension safe
        $extension = $file->guessExtension() ?: 'jpg';

        //UPLOAD AVATAR
        $filename = 'avatar_' . $slug . '.' . $extension;

        $file->move($this->uploadDirectory, $filename);

        return $filename;
    }
}