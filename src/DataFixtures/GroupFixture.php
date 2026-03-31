<?php

namespace App\DataFixtures;

use App\Entity\Group;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GroupFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $groups = [
            'Grabs',
            'Rotations',
            'Flips',
            'Slides'
        ];

        foreach ($groups as $groupName) {
            $group = new Group();
            $group->setName($groupName);

            $manager->persist($group);
        }
        
        $manager->flush();
    }
}