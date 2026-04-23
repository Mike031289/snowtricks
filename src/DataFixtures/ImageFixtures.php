<?php
// src/DataFixtures/ImageFixture.php

/*
 * This file is part of the SnowTricks project.
 *
 * (c) Adjoukou AGBELOU <mike.agbelou@gmail.com> Dev-Application PHP Symfony
 * 
*/

namespace App\DataFixtures;

use App\Entity\Image;
use App\Entity\Trick;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ImageFixture extends Fixture implements DependentFixtureInterface
{
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
            'Noseslide'
        ];

        foreach ($tricks as $name) {

            $trick = $this->getReference($name, Trick::class);

            for ($i = 1; $i <= 2; $i++) {

                $image = new Image();

                $slug = strtolower(str_replace(' ', '', $name));

                $image->setUrl($slug . $i . '.jpg');
                $image->setAlt($name . ' image ' . $i);
                $image->setIsMain($i === 1);
                $image->setCreatedAt(new \DateTimeImmutable());
                $image->setTrick($trick);

                $manager->persist($image);
            }
        }

        $manager->flush();
    }
}