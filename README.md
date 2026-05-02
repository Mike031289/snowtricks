# SnowTricks :
    Projet Symfony

## Description :
    Cette Application sous Symfony 6.4 est une plate forme communautaire de Snowbord où les utilisateurs inscrits peuvent Ajouter et gérer des figures de snowboard avec un espace de discution (commentaires modérés).

## Stack technique :
    - PHP 8.2
    - Symfony 6.4 LTS
    - Doctrine ORM
    - MySQL
    - Bootstrap 5
    - Importmap
    - PHPUnit

## Installation :
    1. git clone ...
    2. composer install
    3. php bin/console doctrine:database:create
    4. php bin/console doctrine:migrations:migrate
    5. php bin/console doctrine:fixtures:load
    6. php bin/console asset-map:compile //#Avec AssetMapper, les assets sont servis dynamiquement en dev mais doivent être compilés en production
    7. php binphp bin/console cache:clear --env=prod

## NB: Les images de démonstration sont incluses dans : 
    public/
        uploads/
            tricks/
                fixtures/
                    indy-grab_1.jpg
                    indy-grab_2.jpg
                    backflip_1.jpg

## Comptes et Connexion :
    Les détails de connexion aux comptes de test sont disponibles dans "src/DataFixtures/UserFixtures.php"

## Lancer les tests :
    php bin/phpunit

## Architecture :
    - Controller
    - Service Layer
    - Repository Pattern
    - Security/Voters (Auth && Security)
    - Form Types

## Schéma de l'arboressence de mon projet :

    SNOWTRICKS/
    |
    ├─ assets/
    ├── styles/app.css
    ├── app.js # SnowTricks App JS + importmap bootstrap + custom css (app.css + mobile)
    |
    ├── bootstrap.js
    ├── tricks/ # ❌ images des tricks uploadés. Dynamique donc ignoré (upload user)
    └── avatars/ # ❌ user avatar ignoré upload user
    |
    public/
    ├── images/ image principal de la banière de la homepage SnowTrick
    ├── uploads/
    │ ├── avatars/# ❌ user avatar ignoré upload user
    │ └──tricks/ # ❌ images des tricks uploadés. Dynamique donc ignoré (upload user)
    │    ├──fixtures/ # ✅ images de test versionnés
    |
    src/
    ├── Controller/
    ├── DataFixtures/ # Les fixtures de test
    ├── Entity/
    ├── EventSubscriber/
    ├── Form/ # FormType
    ├── Repository/
    ├── Security/ # Système d'Auth
    |
    ├── Service/
    │ ├── MediaService.php
    │ └── AvatarService.php
    |
    ├── Templates/ # Tous les templates Twig
    ├── tests
    └── Readme

## Roadmap :
    - v1.0.0 : MVP complet
