<?php
// src/DataFixtures/CommentFixtures.php

/*
 * This file is part of the SnowTricks project.
 *
 * (c) Adjoukou AGBELOU <mike.agbelou@gmail.com> Dev-Application PHP Symfony
 * 
 */

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Trick;
use App\Entity\Comment;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            TrickFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $users = [
            $this->getReference(UserFixtures::USER_1, User::class),
            $this->getReference(UserFixtures::USER_2, User::class),
            $this->getReference(UserFixtures::USER_3, User::class),
        ];

        $commentsByTrick = [
            'Indy Grab' => [
                "Super clean, idéal pour progresser en grabs !",
                "Technique de base mais très efficace.",
                "Je galère encore sur celui-là 😅",
                "Très stylé en switch !",
                "Bonne prise en main après quelques essais.",
                "Fluidité incroyable quand c’est bien exécuté.",
                "Un classique incontournable.",
                "Demande un bon contrôle aérien.",
                "Très bon trick pour progresser.",
                "Impressionnant mais dangereux 😱",
                "Beaucoup de confiance nécessaire.",
                "J’adore le style quand c’est propre !",
                "Base du freestyle 👍",
                "Très bon pour commencer les rotations.",
                "Indispensable en progression.",
            ],
            'Melon Grab' => [
                "Très stylé en switch !",
                "Bonne prise en main après quelques essais.",
                "Fluidité incroyable quand c’est bien exécuté.",
                "Super clean, idéal pour progresser en grabs !",
                "Technique de base mais très efficace.",
                "Je galère encore sur celui-là 😅",
                "Un classique incontournable.",
                "Demande un bon contrôle aérien.",
                "Très bon trick pour progresser.",
                "Impressionnant mais dangereux 😱",
                "Beaucoup de confiance nécessaire.",
                "J’adore le style quand c’est propre !",
                "Base du freestyle 👍",
                "Très bon pour commencer les rotations.",
                "Indispensable en progression.",
            ],
            'Mute Grab' => [
                "Un classique incontournable.",
                "Demande un bon contrôle aérien.",
                "Très bon trick pour progresser.",
                "Super clean, idéal pour progresser en grabs !",
                "Technique de base mais très efficace.",
                "Je galère encore sur celui-là 😅",
                "Très stylé en switch !",
                "Bonne prise en main après quelques essais.",
                "Fluidité incroyable quand c’est bien exécuté.",
                "Un classique incontournable.",
                "Demande un bon contrôle aérien.",
                "Très bon trick pour progresser.",
                "Impressionnant mais dangereux 😱",
                "Beaucoup de confiance nécessaire.",
                "J’adore le style quand c’est propre",
            ],
            'Backflip' => [
                "Impressionnant mais dangereux 😱",
                "Beaucoup de confiance nécessaire.",
                "J’adore le style quand c’est propre !",
                "Un classique incontournable.",
                "Demande un bon contrôle aérien.",
                "Très bon trick pour progresser.",
                "Super clean, idéal pour progresser en grabs !",
                "Technique de base mais très efficace.",
                "Je galère encore sur celui-là 😅",
                "Très stylé en switch !",
                "Bonne prise en main après quelques essais.",
                "Fluidité incroyable quand c’est bien exécuté.",
                "Un classique incontournable.",
                "Demande un bon contrôle aérien.",
                "Très bon trick pour progresser.",
            ],
            'Frontflip' => [
                "Plus technique que je pensais.",
                "Super sensation en l’air !",
                "À maîtriser absolument en freestyle.",
                "Impressionnant mais dangereux 😱",
                "Beaucoup de confiance nécessaire.",
                "J’adore le style quand c’est propre !",
                "Un classique incontournable.",
                "Demande un bon contrôle aérien.",
                "Très bon trick pour progresser.",
                "Super clean, idéal pour progresser en grabs !",
                "Technique de base mais très efficace.",
                "Je galère encore sur celui-là 😅",
                "Très stylé en switch !",
                "Bonne prise en main après quelques essais.",
                "Fluidité incroyable quand c’est bien exécuté.",
            ],
            '360' => [
                "Base du freestyle 👍",
                "Très bon pour commencer les rotations.",
                "Indispensable en progression.",
                "Plus technique que je pensais.",
                "Super sensation en l’air !",
                "À maîtriser absolument en freestyle.",
                "Impressionnant mais dangereux 😱",
                "Beaucoup de confiance nécessaire.",
                "J’adore le style quand c’est propre !",
                "Un classique incontournable.",
                "Demande un bon contrôle aérien.",
                "Très bon trick pour progresser.",
                "Super clean, idéal pour progresser en grabs !",
                "Technique de base mais très efficace.",
                "Je galère encore sur celui-là 😅",
            ],
            '540' => [
                "Déjà un vrai niveau !",
                "Demande beaucoup de timing.",
                "Super flow en l’air.",
                "Base du freestyle 👍",
                "Très bon pour commencer les rotations.",
                "Indispensable en progression.",
                "Plus technique que je pensais.",
                "Super sensation en l’air !",
                "À maîtriser absolument en freestyle.",
                "Impressionnant mais dangereux 😱",
                "Beaucoup de confiance nécessaire.",
                "J’adore le style quand c’est propre !",
                "Un classique incontournable.",
                "Demande un bon contrôle aérien.",
                "Très bon trick pour progresser.",
            ],
            '720' => [
                "Là on passe un cap sérieux.",
                "Très impressionnant en park.",
                "Rotation propre = respect 🔥",
                "Déjà un vrai niveau !",
                "Demande beaucoup de timing.",
                "Super flow en l’air.",
                "Base du freestyle 👍",
                "Très bon pour commencer les rotations.",
                "Indispensable en progression.",
                "Plus technique que je pensais.",
                "Super sensation en l’air !",
                "À maîtriser absolument en freestyle.",
                "Impressionnant mais dangereux 😱",
                "Beaucoup de confiance nécessaire.",
                "J’adore le style quand c’est propre !",
            ],
            'Boardslide' => [
                "Classique mais toujours efficace.",
                "Bon équilibre nécessaire.",
                "Parfait pour débuter les rails.",
                "Là on passe un cap sérieux.",
                "Très impressionnant en park.",
                "Rotation propre = respect 🔥",
                "Déjà un vrai niveau !",
                "Demande beaucoup de timing.",
                "Super flow en l’air.",
                "Base du freestyle 👍",
                "Très bon pour commencer les rotations.",
                "Indispensable en progression.",
                "Plus technique que je pensais.",
                "Super sensation en l’air !",
                "À maîtriser absolument en freestyle.",
            ],
            'Noseslide' => [
                "Très technique sur rail.",
                "Demande de la précision.",
                "Super style quand c’est locké.",
                "Classique mais toujours efficace.",
                "Bon équilibre nécessaire.",
                "Parfait pour débuter les rails.",
                "Là on passe un cap sérieux.",
                "Très impressionnant en park.",
                "Rotation propre = respect 🔥",
                "Déjà un vrai niveau !",
                "Demande beaucoup de timing.",
                "Super flow en l’air.",
                "Base du freestyle 👍",
                "Très bon pour commencer les rotations.",
                "Indispensable en progression.",
            ],
            'Tail Grab' => [
                "Très stylé en sortie de saut.",
                "Bon contrôle requis.",
                "J’adore le look aérien.",
                "Très technique sur rail.",
                "Demande de la précision.",
                "Super style quand c’est locké.",
                "Classique mais toujours efficace.",
                "Bon équilibre nécessaire.",
                "Parfait pour débuter les rails.",
                "Là on passe un cap sérieux.",
                "Très impressionnant en park.",
                "Rotation propre = respect 🔥",
                "Déjà un vrai niveau !",
                "Demande beaucoup de timing.",
                "Super flow en l’air.",
            ],
            'Stalefish' => [
                "Un grab old school mais stylé.",
                "Pas facile mais très propre.",
                "Très esthétique en freestyle.",
                "Très stylé en sortie de saut.",
                "Bon contrôle requis.",
                "J’adore le look aérien.",
                "Très technique sur rail.",
                "Demande de la précision.",
                "Super style quand c’est locké.",
                "Classique mais toujours efficace.",
                "Bon équilibre nécessaire.",
                "Parfait pour débuter les rails.",
                "Là on passe un cap sérieux.",
                "Très impressionnant en park.",
                "Rotation propre = respect 🔥",
            ],
            '900' => [
                "Niveau expert clairement 🔥",
                "Incroyable à voir en live.",
                "Rotation ultra technique.",
                 "Un grab old school mais stylé.",
                "Pas facile mais très propre.",
                "Très esthétique en freestyle.",
                "Très stylé en sortie de saut.",
                "Bon contrôle requis.",
                "J’adore le look aérien.",
                "Très technique sur rail.",
                "Demande de la précision.",
                "Super style quand c’est locké.",
                "Classique mais toujours efficace.",
                "Bon équilibre nécessaire.",
                "Parfait pour débuter les rails.",
            ],
            'Lip Slide' => [
                "Très bon sur rails techniques.",
                "Demande précision et équilibre.",
                "Stylé en park urbain.",
                "Niveau expert clairement 🔥",
                "Incroyable à voir en live.",
                "Rotation ultra technique.",
                "Un grab old school mais stylé.",
                "Pas facile mais très propre.",
                "Très esthétique en freestyle.",
                "Très stylé en sortie de saut.",
                "Bon contrôle requis.",
                "J’adore le look aérien.",
                "Très technique sur rail.",
                "Demande de la précision.",
                "Super style quand c’est locké.",
            ],
            'Cork 720' => [
                "Rotation très technique !",
                "Beaucoup de style en cork.",
                "Très impressionnant visuellement.",
                "Très bon sur rails techniques.",
                "Demande précision et équilibre.",
                "Stylé en park urbain.",
                "Niveau expert clairement 🔥",
                "Incroyable à voir en live.",
                "Rotation ultra technique.",
                "Un grab old school mais stylé.",
                "Pas facile mais très propre.",
                "Très esthétique en freestyle.",
                "Très stylé en sortie de saut.",
                "Bon contrôle requis.",
                "J’adore le look aérien.",
            ],
        ];

        foreach ($commentsByTrick as $trickName => $comments) {

            $trick = $this->getReference($trickName, Trick::class);

            foreach ($comments as $content) {

                $comment = new Comment();

                $comment->setContent($content);
                $comment->setCreatedAt(new \DateTimeImmutable());
                $comment->setAuthor($users[array_rand($users)]);
                $comment->setTrick($trick);

                $manager->persist($comment);
            }
        }

        $manager->flush();
    }
}