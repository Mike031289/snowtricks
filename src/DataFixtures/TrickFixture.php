<?php

namespace App\DataFixtures;

use App\Entity\Trick;
use App\Entity\Image;
use App\Entity\Group;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

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
                'description' => 'Saisie de la carre backside avec la main arrière.',
                'group' => 'Grabs',
                'images' => ['indy1.jpg', 'indy2.jpg']
            ],
            [
                'name' => 'Backflip',
                'description' => 'Rotation arrière complète en l’air.',
                'group' => 'Flips',
                'images' => ['backflip1.jpg', 'backflip2.jpg']
            ],
            [
                'name' => '360',
                'description' => 'Rotation complète de 360 degrés.',
                'group' => 'Rotations',
                'images' => ['360_1.jpg', '360_2.jpg']
            ],
            [
                'name' => 'Boardslide',
                'description' => 'Glissade sur une barre avec la planche perpendiculaire.',
                'group' => 'Slides',
                'images' => ['slide1.jpg', 'slide2.jpg']
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