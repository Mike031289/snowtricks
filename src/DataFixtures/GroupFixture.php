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

class GroupFixture extends Fixture
{
    // private const GROUPS = [
    //     'Grabs',
    //     'Flips',
    //     'Rotations',
    //     'Slides'
    // ];
    
    public function load(ObjectManager $manager): void
    {
        // $groups = self::GROUPS;
        $groups = [
            'Grabs',
            'Flips',
            'Rotations',
            'Slides'
        ];
        
        // slug arrow function
        $slug = fn($string) => strtolower(str_replace(' ', '-', $string));

        foreach ($groups as $groupName) {
            $group = new Group();
            $group->setName($groupName);
            $group->setSlug($slug($groupName));

            $manager->persist($group);
            
            // other fixtures can get this object using the GroupFixtures::GROUP_REFERENCE constant
            $this->addReference($groupName, $group);
        }

        $manager->flush();
    }
}