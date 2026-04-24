<?php
// src/DataFixtures/CommentFixtures.php

/*
 * This file is part of the SnowTricks project.
 *
 * (c) Adjoukou AGBELOU <mike.agbelou@gmail.com> Dev-Application PHP Symfony
 * 
 */

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Trick;
use App\Entity\Comment;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            TrickFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $users = [
            $this->getReference(UserFixtures::USER_1, User::class),
            $this->getReference(UserFixtures::USER_2, User::class),
            $this->getReference(UserFixtures::USER_3, User::class),
        ];

        $tricksNames = [
            'Indy Grab',
            'Melon Grab',
            'Mute Grab',
            'Backflip',
            'Frontflip',
            '360',
            '540',
            '720',
            'Boardslide',
            'Noseslide'
        ];

        foreach ($tricksNames as $trickName) {

            $trick = $this->getReference($trickName, Trick::class);

            for ($i = 1; $i <= 5; $i++) {

                $comment = new Comment();

                $author = $users[array_rand($users)];

                $comment->setContent("Commentaire {$i} sur {$trickName}.");
                $comment->setCreatedAt(new \DateTimeImmutable());
                $comment->setAuthor($author);
                $comment->setTrick($trick);

                $manager->persist($comment);
            }
        }

        $manager->flush();
    }
}