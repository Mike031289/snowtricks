<?php
// src/DataFixtures/TrickFixtures.php

/*
 * This file is part of the SnowTricks project.
 *
 * (c) Adjoukou AGBELOU <mike.agbelou@gmail.com> Dev-Application PHP Symfony
 * 
 */

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Trick;
use App\Entity\Image;
use App\Entity\Video;
use App\Entity\Comment;
use App\Entity\Group;
use App\DataFixtures\UserFixtures;
use App\DataFixtures\GroupFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class TrickFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private SluggerInterface $slugger) {}

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            GroupFixtures::class,
        ];
    }


    public function load(ObjectManager $manager): void
    {
        $users = [
            $this->getReference(UserFixtures::USER_1, User::class),
            $this->getReference(UserFixtures::USER_2, User::class),
            $this->getReference(UserFixtures::USER_3, User::class),
        ];

        $videoLinks = [
            'https://www.youtube.com/embed/dQw4w9WgXcQ',
            'https://www.youtube.com/embed/3GwjfUFyY6M',
            'https://www.dailymotion.com/embed/video/x7u5zph',
            'https://www.dailymotion.com/embed/video/x84sh7d',
        ];

        $tricksData = [
            ['name' => 'Indy Grab', 'description' => 'Saisie backside entre les pieds.', 'group' => 'Grabs'],
            ['name' => 'Melon Grab', 'description' => 'Saisie frontside.', 'group' => 'Grabs'],
            ['name' => 'Mute Grab', 'description' => 'Grab frontside entre les pieds.', 'group' => 'Grabs'],
            ['name' => 'Backflip', 'description' => 'Rotation arrière complète.', 'group' => 'Flips'],
            ['name' => 'Frontflip', 'description' => 'Rotation avant complète.', 'group' => 'Flips'],
            ['name' => '360', 'description' => 'Rotation 360 degrés.', 'group' => 'Rotations'],
            ['name' => '540', 'description' => 'Rotation 540 degrés.', 'group' => 'Rotations'],
            ['name' => '720', 'description' => 'Double rotation.', 'group' => 'Rotations'],
            ['name' => 'Boardslide', 'description' => 'Slide sur rail.', 'group' => 'Slides'],
            ['name' => 'Noseslide', 'description' => 'Slide avant planche.', 'group' => 'Slides'],
        ];

        foreach ($tricksData as $index => $data) {

            $trick = new Trick();

            // USER rotation (3 users)
            $user = $users[$index % 3];
            $trick->setAuthor($user);

            // GROUP
            $group = $this->getReference($data['group'], Group::class);
            $trick->setGroups($group);

            // BASIC DATA
            $trick->setName($data['name']);
            $trick->setDescription($data['description']);

            $slug = $this->slugger->slug($data['name'])->lower();
            $trick->setSlug($slug);

            $trick->setCreatedAt(new \DateTimeImmutable());
            $trick->setUpdatedAt(new \DateTimeImmutable());

            $trick->setMainImage($slug . '_main.jpg');

            $manager->persist($trick);

            // ================= IMAGES (15) =================
            for ($i = 1; $i <= 15; $i++) {
                $image = new Image();
                $image->setUrl($slug . "_img_" . $i . ".jpg");
                $image->setAlt($data['name'] . " image " . $i);
                $image->setIsMain($i === 1);
                $image->setCreatedAt(new \DateTimeImmutable());
                $image->setTrick($trick);

                $manager->persist($image);
            }

            // ================= VIDEOS (15) =================
            for ($i = 1; $i <= 15; $i++) {
                $video = new Video();
                $video->setEmbedUrl($videoLinks[array_rand($videoLinks)]);
                $video->setIsMain($i === 1);
                $video->setCreatedAt(new \DateTimeImmutable());
                $video->setTrick($trick);

                $manager->persist($video);
            }

            // ================= COMMENTS (5) =================
            for ($i = 1; $i <= 5; $i++) {
                $comment = new Comment();
                $comment->setContent("Commentaire {$i} sur {$data['name']}");
                $comment->setCreatedAt(new \DateTimeImmutable());
                $comment->setAuthor($users[array_rand($users)]);
                $comment->setTrick($trick);

                $manager->persist($comment);
            }

            // reference trick
            $this->addReference($data['name'], $trick);
        }

        $manager->flush();
    }
}