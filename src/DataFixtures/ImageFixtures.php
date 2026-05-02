<?php
// src/DataFixtures/ImageFixture.php

/*
* This file is part of the SnowTricks project.
* (c) Adjoukou AGBELOU <adjoukou.agbelou@live.com> Dev-Application PHP Symfony
*/

namespace App\DataFixtures;

use App\Entity\Image;
use App\Entity\Trick;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ImageFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private SluggerInterface $slugger) {}

    public function getDependencies(): array
    {
        return [TrickFixtures::class];
    }

    public function load(ObjectManager $manager): void
    {
        
        $tricks = [
            'Indy Grab',
            'Melon Grab',
            'Mute Grab',

            'Backflip',
            'Frontflip',

            '360',
            '540',
            '720',

            'Boardslide',
            'Noseslide',

            'Tail Grab',
            'Stalefish',

            '900',

            'Lip Slide',
            'Cork 720',
        ];

        foreach ($tricks as $name) {

            $trick = $this->getReference($name, Trick::class);

            $slug = $this->slugger->slug($name)->lower();

            for ($i = 1; $i <= 3; $i++) {

                $image = new Image();

                /**
                 * FIXTURES STORAGE PATH just for development and testing purposes.
                 * In production, images should be uploaded by users and stored in a proper directory like upload/images/.
                 */
                $image->setUrl('fixtures/' . $slug . '_' . $i . '.jpg');

                $image->setAlt(sprintf('%s image %d', $name, $i));
                $image->setIsMain($i === 1);
                $image->setCreatedAt(new \DateTimeImmutable());
                $image->setTrick($trick);

                $manager->persist($image);
            }
        }

        $manager->flush();
    }
}