<?php

// src/DataFixtures/GroupFixture.php

/*
 * This file is part of the SnowTricks project.
 *
 * (c) Adjoukou AGBELOU <mike.agbelou@gmail.com> Dev-Application PHP Symfony
 *
 */

namespace App\DataFixtures;

use App\Entity\Group;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class GroupFixtures extends Fixture
{
    public function __construct(private SluggerInterface $slugger)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $groups = [
            'Grabs',
            'Flips',
            'Rotations',
            'Slides',
            'Jumps',
            'Freestyle Basics',
            'Freeride Tricks',
            'Switch Tricks',
            'Big Air',
        ];

        foreach ($groups as $groupName) {

            $group = new Group();

            $group->setName($groupName);
            $group->setSlug(
                $this->slugger->slug($groupName)->lower()
            );

            $manager->persist($group);

            // Reference
            $this->addReference($groupName, $group);
        }

        $manager->flush();
    }
}
