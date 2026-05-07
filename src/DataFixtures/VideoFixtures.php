<?php

// src/DataFixtures/VideoFixtures.php

namespace App\DataFixtures;

use App\Entity\Trick;
use App\Entity\Video;
use App\Service\MediaService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class VideoFixtures extends Fixture implements DependentFixtureInterface
{
    // We inject the MediaService to use its conversion logic
    public function __construct(
        private MediaService $mediaService
    ) {
    }

    public function getDependencies(): array
    {
        return [TrickFixtures::class];
    }

    public function load(ObjectManager $manager): void
    {
        /**
         * Real video links (YouTube + Dailymotion)
         */
        $videoUrls = [
            'https://www.youtube.com/watch?v=v6xqATm7JQc',
            'https://www.youtube.com/watch?v=7VBalG0IhhI',
            'https://www.dailymotion.com/video/x6zxwl',
        ];

        /** @var array<int, string> $tricksNames */
        $tricksNames = [
            'Indy Grab', 'Melon Grab', 'Mute Grab',
            'Backflip', 'Frontflip',
            '360', '540', '720',
            'Boardslide', 'Noseslide',
            'Tail Grab', 'Stalefish',
            '900', 'Lip Slide', 'Cork 720',
        ];

        foreach ($tricksNames as $name) {
            /** @var Trick $trick */
            $trick = $this->getReference((string) $name, Trick::class);

            // 3 videos per trick
            foreach ($videoUrls as $index => $url) {

                // We use the MediaService to transform the URL into an Embed URL
                $embedUrl = $this->mediaService->convertToEmbedUrl($url);

                if (null !== $embedUrl) {
                    $video = new Video();
                    $video->setEmbedUrl($embedUrl);
                    $video->setIsMain(0 === $index);
                    $video->setCreatedAt(new \DateTimeImmutable());
                    $video->setTrick($trick);

                    $manager->persist($video);
                }
            }
        }

        $manager->flush();
    }
}
