<?php
// src/DataFixtures/TrickFixtures.php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Trick;
use App\Entity\Group;
use App\DataFixtures\UserFixtures;
use App\DataFixtures\GroupFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class TrickFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private SluggerInterface $slugger) {}

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
            ['name' => '720', 'description' => 'Double rotation.', 'group' => 'Rotations'],
            ['name' => 'Boardslide', 'description' => 'Slide sur rail.', 'group' => 'Slides'],
            ['name' => 'Noseslide', 'description' => 'Slide avant planche.', 'group' => 'Slides'],
        ];

        foreach ($tricksData as $index => $data) {

            $trick = new Trick();

            // USER rotation (3 users)
            $user = $users[$index % 3];
            $trick->setAuthor($user);

            // GROUP
            $group = $this->getReference($data['group'], Group::class);
            $trick->setGroups($group);

            // BASIC DATA
            $trick->setName($data['name']);
            $trick->setDescription($data['description']);

            $slug = $this->slugger->slug($data['name'])->lower();
            $trick->setSlug($slug);

            $trick->setCreatedAt(new \DateTimeImmutable());
            $trick->setUpdatedAt(new \DateTimeImmutable());

            $trick->setMainImage('fixtures/' .$slug . '_1.jpg');

            $manager->persist($trick);

            $this->addReference($data['name'], $trick);
        }

        $manager->flush();
    }
}