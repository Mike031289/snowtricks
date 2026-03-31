<?php

namespace App\DataFixtures;

use App\Entity\Group;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GroupFixture extends Fixture
{
    public const GROUP_REFERENCE = 'group-demo';
    
    private const GROUPS = [
        'Grabs',
        'Rotations',
        'Flips',
        'Slides'
    ];
    
    public function load(ObjectManager $manager): void
    {
        $groups = self::GROUPS;
        
        $slug = fn($string) => strtolower(str_replace(' ', '-', $string));

        foreach ($groups as $groupName) {
            $group = new Group();
            $group->setName($groupName);
            $group->setSlug($slug($groupName));

            $manager->persist($group);
        }
        
        // other fixtures can get this object using the GroupFixtures::GROUP_REFERENCE constant
        $this->addReference(self::GROUP_REFERENCE, $group);
        
        $manager->flush();
    }
}