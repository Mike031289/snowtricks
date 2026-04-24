<?php
// src/DataFixtures/VideoFixtures.php

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
        /**
         * 3 REAL VIDEO LINKS (YouTube + Dailymotion)
         * FORMAT WATCH (important for your converter)
         */
        $videoUrls = [
            'https://www.youtube.com/embed/aqz-KE-bpKQ',
            'https://www.dailymotion.com/embed/video/x7tgczk',
            'https://www.dailymotion.com/embed/video/x80q4x9',
        ];

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

            // 3 videos per trick (stable & deterministic)
            for ($i = 0; $i < 3; $i++) {

                $video = new Video();

                $video->setEmbedUrl($videoUrls[$i]);
                $video->setIsMain($i === 0);
                $video->setCreatedAt(new \DateTimeImmutable());
                $video->setTrick($trick);

                $manager->persist($video);
            }
        }

        $manager->flush();
    }
}