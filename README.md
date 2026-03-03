# SnowTricks

## Description
Application Symfony 6.4 permettant de gérer et partager des figures de snowboard.

## Stack technique
- PHP 8.2
- Symfony 6.4 LTS
- Doctrine ORM
- MySQL
- Bootstrap 5
- PHPUnit

## Installation
composer install
cp .env .env.local
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

## Lancer les tests
php bin/phpunit

## Architecture
- Controller
- Service Layer
- Repository Pattern
- Voters (sécurité)
- Form Types

## Roadmap
- v1.0.0 : MVP complet
