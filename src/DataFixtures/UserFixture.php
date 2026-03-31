<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixture extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher) {}
    
    public function load(ObjectManager $manager): void
    {
        $user = new User();

        $user->setUsername('demo');
        $user->setEmail('demo@snowtricks.com');
        $user->setPassword($this->hasher->hashPassword($user, 'password'));
        $user->setRoles(['ROLE_USER']);
        $user->setIsVerified(true);
        $user->setCreatedAt(new \DateTimeImmutable());

        $manager->persist($user);

        $this->addReference('user_demo', $user);

        $manager->flush();

        $manager->flush();
    }
}