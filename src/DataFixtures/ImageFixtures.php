<?php

// src/DataFixtures/ImageFixture.php

/*
* This file is part of the SnowTricks project.
* (c) Adjoukou AGBELOU <adjoukou.agbelou@live.com> Dev-Application PHP Symfony
*/

namespace App\DataFixtures;

use App\Entity\Image;
use App\Entity\Trick;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\String\Slugger\SluggerInterface;

class ImageFixtures extends Fixture implements DependentFixtureInterface
{
    // We add Filesystem to handle file copying
    public function __construct(
        private SluggerInterface $slugger,
        private string $projectDir, // We will need the project path
    ) {
    }

    public function getDependencies(): array
    {
        return [TrickFixtures::class];
    }

    public function load(ObjectManager $manager): void
    {
        $filesystem = new Filesystem();

        // Define paths
        $sourceDir = $this->projectDir.'/assets/fixtures/tricks';
        $targetDir = $this->projectDir.'/public/uploads/tricks';

        // Create target directory if it doesn't exist
        if (!$filesystem->exists($targetDir)) {
            $filesystem->mkdir($targetDir);
        }

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

            for ($i = 1; $i <= 3; ++$i) {
                $filename = $slug.'_'.$i.'.jpg';
                $sourcePath = $sourceDir.'/'.$filename;
                $targetPath = $targetDir.'/'.$filename;

                // Only copy and persist if the source image exists in /assets
                if ($filesystem->exists($sourcePath)) {
                    // COPY the file (this is the key to solve your problem)
                    $filesystem->copy($sourcePath, $targetPath, true);

                    $image = new Image();
                    $image->setUrl($filename); // Store only the filename
                    $image->setAlt(sprintf('%s image %d', $name, $i));
                    $image->setIsMain(1 === $i);
                    $image->setCreatedAt(new \DateTimeImmutable());
                    $image->setTrick($trick);

                    $manager->persist($image);
                }
            }
        }

        $manager->flush();
    }
}
