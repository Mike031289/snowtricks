<?php

namespace App\DataFixtures;

use App\Entity\Image;
use App\Entity\Trick;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ImageFixture extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [TrickFixture::class];
    }
    
    public function load(ObjectManager $manager): void
    {
        $tricks = ['Indy Grab', 'Backflip', '360', 'Boardslide'];

        foreach ($tricks as $name) {

            $trick = $this->getReference($name, Trick::class);

            for ($i = 1; $i <= 2; $i++) {
                $image = new Image();

                $image->setUrl(strtolower(str_replace(' ', '', $name)).$i.'.jpg');
                $image->setAlt($name.' image '.$i);
                $image->setIsMain($i === 1);
                $image->setCreatedAt(new \DateTimeImmutable());
                $image->setTrick($trick);

                $manager->persist($image);
                
            }
                
            // other fixtures can get this object using the ImageFixtures::IMAGE_REFERENCE constant
            $this->addReference($name, $image);
        }

        $manager->flush();
    }

}