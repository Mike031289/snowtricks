<?php

namespace App\DataFixtures;

use App\Entity\Video;
use App\Entity\Trick;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class VideoFixture extends Fixture implements DependentFixtureInterface
{
    
    public function getDependencies(): array
    {
        return [TrickFixture::class];
    }
    
    public function load(ObjectManager $manager): void
    {
        $videos = [
            'Indy Grab' => 'https://www.youtube.com/embed/xxx1',
            'Backflip' => 'https://www.youtube.com/embed/xxx2',
        ];

        foreach ($videos as $name => $url) {

            $trick = $this->getReference($name, Trick::class);

            $video = new Video();
            $video->setEmbedUrl($url);
            $video->setIsMain(true);
            $video->setCreatedAt(new \DateTimeImmutable());
            $video->setTrick($trick);

            $manager->persist($video);
            
            // other fixtures can get this object using the VideoFixtures::VIDEO_REFERENCE constant
            $this->addReference($name, $video);
        }

        $manager->flush();
        
    }

}