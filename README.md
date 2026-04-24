# SnowTricks
Projet 2026

## Description
Cette Application sous Symfony 6.4 est une plate forme communautaire de Snowbord où les utilisateurs inscrits peuvent Ajouter et gérer des figures de snowboard avec un espace de discution (commentaires modérés).

## Stack technique
- PHP 8.2
- Symfony 6.4 LTS
- Doctrine ORM
- MySQL
- Bootstrap 5
- Importmap
- PHPUnit

## Installation
1. git clone ...
2. composer install
3. php bin/console doctrine:database:create
4. php bin/console doctrine:migrations:migrate
5. php bin/console doctrine:fixtures:load

NB: Les images de démonstration sont incluses dans : 
    public/
        uploads/
            fixtures/
                indy-grab_1.jpg
                indy-grab_2.jpg
                backflip_1.jpg

Les détails de connexion aux comptes de test sont disponibles dans "src/DataFixtures/UserFixtures.php"

## Lancer les tests
php bin/phpunit

## Architecture
- Controller
- Service Layer
- Repository Pattern
- Security/Voters (Auth && Security)
- Form Types

Schéma de l'arboressence
src/
├── Controller/
├── Entity/
├── Repository/
├── Service/
│ ├── MediaService.php
│ └── AvatarService.php
├── DataFixtures/
├── Security/
└── Form/

public/
└── uploads/
├── images/ # images upload user
├── fixtures/ # fake images for dev
└── avatar/ # user avatar

## Roadmap
- v1.0.0 : MVP complet
