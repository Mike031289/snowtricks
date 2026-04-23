<?php
// src/DataFixtures/TrickFixture.php

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
use App\Entity\Group;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class TrickFixture extends Fixture implements DependentFixtureInterface
{
    public function __construct(private SluggerInterface $slugger) {}

    public function getDependencies(): array
    {
        return [
            UserFixture::class,
            GroupFixture::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $tricksData = [
            [
                'name' => 'Indy Grab',
                'description' => 'Saisie de la carre backside entre les pieds avec la main arrière lors d’un saut.',
                'group' => 'Grabs',
                'images' => ['indy1.jpg', 'indy2.jpg']
            ],
            [
                'name' => 'Melon Grab',
                'description' => 'Saisie de la carre frontside avec la main avant en maintenant la planche sous le corps.',
                'group' => 'Grabs',
                'images' => ['melon1.jpg', 'melon2.jpg']
            ],
            [
                'name' => 'Mute Grab',
                'description' => 'Saisie de la carre frontside entre les pieds avec la main avant.',
                'group' => 'Grabs',
                'images' => ['mute1.jpg', 'mute2.jpg']
            ],
            [
                'name' => 'Backflip',
                'description' => 'Rotation arrière complète en l’air, souvent réalisée sur un kicker.',
                'group' => 'Flips',
                'images' => ['backflip1.jpg', 'backflip2.jpg']
            ],
            [
                'name' => 'Frontflip',
                'description' => 'Rotation avant complète nécessitant engagement et contrôle du corps.',
                'group' => 'Flips',
                'images' => ['frontflip1.jpg', 'frontflip2.jpg']
            ],
            [
                'name' => '360',
                'description' => 'Rotation complète de 360 degrés autour de l’axe vertical.',
                'group' => 'Rotations',
                'images' => ['360_1.jpg', '360_2.jpg']
            ],
            [
                'name' => '540',
                'description' => 'Rotation de 540 degrés, soit un tour et demi en l’air.',
                'group' => 'Rotations',
                'images' => ['540_1.jpg', '540_2.jpg']
            ],
            [
                'name' => '720',
                'description' => 'Double rotation complète (720 degrés) demandant vitesse et précision.',
                'group' => 'Rotations',
                'images' => ['720_1.jpg', '720_2.jpg']
            ],
            [
                'name' => 'Boardslide',
                'description' => 'Glissade sur une barre avec la planche perpendiculaire à l’obstacle.',
                'group' => 'Slides',
                'images' => ['boardslide1.jpg', 'boardslide2.jpg']
            ],
            [
                'name' => 'Noseslide',
                'description' => 'Glissade sur une barre en appui sur l’avant de la planche.',
                'group' => 'Slides',
                'images' => ['noseslide1.jpg', 'noseslide2.jpg']
            ],
        ];

        foreach ($tricksData as $data) {

            $trick = new Trick();

            $trick->setName($data['name']);
            $trick->setDescription($data['description']);

            // slug
            $slug = $this->slugger->slug($data['name'])->lower();
            $trick->setSlug($slug);

            // dates
            $trick->setCreatedAt(new \DateTimeImmutable());
            $trick->setUpdatedAt(new \DateTimeImmutable());

            // author (IMPORTANT)
            $user = $this->getReference(UserFixture::USER_REFERENCE, User::class);
            $trick->setAuthor($user);

            // group (IMPORTANT)
            $group = $this->getReference($data['group'], Group::class);
            $trick->setGroups($group);

            // main image (string dans Trick)
            $trick->setMainImage($data['images'][0]);

            // image entities (relation)
            foreach ($data['images'] as $index => $imageName) {

                $image = new Image();

                $image->setUrl($imageName);
                $image->setAlt($data['name'].' image '.$index);

                // bool
                $image->setIsMain($index === 0);

                // date
                $image->setCreatedAt(new \DateTimeImmutable());

                $image->setTrick($trick);

                $manager->persist($image);
            }

            // other fixtures can get this object using the TrickFixtures::TRICKS_REFERENCE constant
            $this->addReference($data['name'], $trick);

            $manager->persist($trick);
        }

        $manager->flush();
    }
}