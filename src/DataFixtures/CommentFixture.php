<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\User;
use App\Entity\Trick;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CommentFixture extends Fixture implements DependentFixtureInterface
{
    
    public function getDependencies(): array
    {
        return [UserFixture::class, TrickFixture::class];
    }
    
    public function load(ObjectManager $manager): void
    {
        $user = $this->getReference(UserFixture::USER_REFERENCE, User::class);

        $tricks = ['Indy Grab', 'Backflip'];

        foreach ($tricks as $name) {

            $trick = $this->getReference($name, Trick::class);

            for ($i = 1; $i <= 3; $i++) {

                $comment = new Comment();
                $comment->setContent("Super trick $name !");
                $comment->setCreatedAt(new \DateTimeImmutable());
                $comment->setAuthor($user);
                $comment->setTrick($trick);

                $manager->persist($comment);
            }
            
            // other fixtures can get this object using the CommentFixtures::COMMENT_REFERENCE constant
            $this->addReference($name, $comment);
            
        }

        $manager->flush();
        
    }

}