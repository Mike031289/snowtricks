<?php

// src/DataFixtures/UserFixtures.php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const USER_1 = 'user_1';
    public const USER_2 = 'user_2';
    public const USER_3 = 'user_3';

    public function __construct(private UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $password = 'password'; // même mot de passe pour tous

        // USER 1
        $user1 = new User();
        $user1->setUsername('user_demo');
        $user1->setEmail('demo@snowtricks.com');
        $user1->setPassword($this->hasher->hashPassword($user1, $password));
        $user1->setRoles(['ROLE_USER']);
        $user1->setIsVerified(true);
        $user1->setCreatedAt(new \DateTimeImmutable());

        $manager->persist($user1);
        $this->addReference(self::USER_1, $user1);

        // USER 2
        $user2 = new User();
        $user2->setUsername('john_doe');
        $user2->setEmail('john@snowtricks.com');
        $user2->setPassword($this->hasher->hashPassword($user2, $password));
        $user2->setRoles(['ROLE_USER']);
        $user2->setIsVerified(true);
        $user2->setCreatedAt(new \DateTimeImmutable());

        $manager->persist($user2);
        $this->addReference(self::USER_2, $user2);

        // USER 3
        $user3 = new User();
        $user3->setUsername('mike');
        $user3->setEmail('mike@snowtricks.com');
        $user3->setPassword($this->hasher->hashPassword($user3, $password));
        $user3->setRoles(['ROLE_USER']);
        $user3->setIsVerified(true);
        $user3->setCreatedAt(new \DateTimeImmutable());

        $manager->persist($user3);
        $this->addReference(self::USER_3, $user3);

        $manager->flush();
    }
}
