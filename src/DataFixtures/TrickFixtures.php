<?php

// src/DataFixtures/TrickFixtures.php

namespace App\DataFixtures;

use App\Entity\Group;
use App\Entity\Trick;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class TrickFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private SluggerInterface $slugger)
    {
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            GroupFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $users = [
            $this->getReference(UserFixtures::USER_1, User::class),
            $this->getReference(UserFixtures::USER_2, User::class),
            $this->getReference(UserFixtures::USER_3, User::class),
        ];

        $tricksData = [
            ['name' => 'Indy Grab', 'description' => 'Saisie backside entre les pieds.', 'group' => 'Grabs'],
            ['name' => 'Melon Grab', 'description' => 'Saisie frontside.', 'group' => 'Grabs'],
            ['name' => 'Mute Grab', 'description' => 'Grab frontside entre les pieds.', 'group' => 'Grabs'],
            ['name' => 'Backflip', 'description' => 'Rotation arrière complète.', 'group' => 'Flips'],
            ['name' => 'Frontflip', 'description' => 'Rotation avant complète.', 'group' => 'Flips'],
            ['name' => '360', 'description' => 'Rotation 360 degrés.', 'group' => 'Rotations'],
            ['name' => '540', 'description' => 'Rotation 540 degrés.', 'group' => 'Rotations'],
            ['name' => '720', 'description' => 'Double rotation aérienne.', 'group' => 'Rotations'],
            ['name' => 'Boardslide', 'description' => 'Slide sur rail avec la planche.', 'group' => 'Slides'],
            ['name' => 'Noseslide', 'description' => 'Slide avant de la board sur rail.', 'group' => 'Slides'],
            ['name' => 'Tail Grab', 'description' => 'Saisie de la queue de la planche.', 'group' => 'Grabs'],
            ['name' => 'Stalefish', 'description' => 'Grab backside main arrière entre les fixations.', 'group' => 'Grabs'],
            ['name' => '900', 'description' => 'Trois rotations complètes.', 'group' => 'Rotations'],
            ['name' => 'Lip Slide', 'description' => 'Slide sur le coping d’un rail.', 'group' => 'Slides'],
            ['name' => 'Cork 720', 'description' => 'Rotation inversée type cork.', 'group' => 'Flips'],
        ];

        foreach ($tricksData as $index => $data) {
            $trick = new Trick();

            // Rotate users (3 users available)
            $user = $users[$index % 3];
            $trick->setAuthor($user);

            // Set Group reference
            $group = $this->getReference($data['group'], Group::class);
            $trick->setGroups($group);

            $trick->setName($data['name']);
            $trick->setDescription($data['description']);

            // Generate Slug
            $slug = $this->slugger->slug($data['name'])->lower();
            $trick->setSlug($slug);

            $trick->setCreatedAt(new \DateTimeImmutable());
            $trick->setUpdatedAt(new \DateTimeImmutable());

            // Define the Main Image filename (matches the file copied in ImageFixtures)
            // Example: "indy-grab_1.jpg"
            $trick->setMainImage($slug . '_1.jpg');

            $manager->persist($trick);

            // Add reference for ImageFixtures and other dependencies
            $this->addReference($data['name'], $trick);
        }

        $manager->flush();
    }
}
