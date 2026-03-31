<?php

namespace App\DataFixtures;

use App\Entity\Trick;
use App\Entity\Image;
use App\Entity\Group;
use App\Entity\User;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class TrickFixture extends Fixture
{
    public function __construct(private SluggerInterface $slugger) {}
    
    public function load(ObjectManager $manager): void
    {
        $user = $manager->getRepository(User::class)->findOneBy([]);
        $group = $manager->getRepository(Group::class)->findOneBy([]);
        
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

            // auteur
            $trick->setAuthor($user);
            
            // groupe
            $trick->setGroups($group);   
            
            // image principale
            $trick->setMainImage($data['images'][0]);

            // images secondaires
            foreach ($data['images'] as $imageName) {
                $image = new Image();
                $image->setUrl($imageName);
                $image->setTrick($trick);

                $manager->persist($image);
            }

            $manager->persist($trick);
        }

        $manager->flush();
    }
    
}