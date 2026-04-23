<?php
// src/DataFixtures/VideoFixture.php

/*
 * This file is part of the SnowTricks project.
 *
 * (c) Adjoukou AGBELOU <mike.agbelou@gmail.com> Dev-Application PHP Symfony
 * 
*/

namespace App\DataFixtures;

use App\Entity\Video;
use App\Entity\Trick;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class VideoFixtures extends Fixture implements DependentFixtureInterface
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

        $videoUrls = [
            'https://www.youtube.com/embed/xxx1',
            'https://www.youtube.com/embed/xxx2',
            'https://www.dailymotion.com/embed/video/x7u5zph',
            'https://www.dailymotion.com/embed/video/x84sh7d',
        ];

        foreach ($tricks as $name) {

            $trick = $this->getReference($name, Trick::class);

            for ($i = 1; $i <= 3; $i++) {

                $video = new Video();

                $video->setEmbedUrl($videoUrls[array_rand($videoUrls)]);
                $video->setIsMain($i === 1);
                $video->setCreatedAt(new \DateTimeImmutable());
                $video->setTrick($trick);

                $manager->persist($video);
            }
        }

        $manager->flush();
    }
}