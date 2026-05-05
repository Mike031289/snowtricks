# ❄ SnowTricks - Plateforme communautaire de snowboard
## 📌 Description
SnowTricks est une application web développée avec Symfony 6.4 permettant aux passionnés de snowboard de partager et documenter des figures (tricks).

Les utilisateurs peuvent :
- Créer un compte
- Ajouter / modifier / supprimer des tricks
- Ajouter des images et vidéos
- Commenter les tricks (modération via sécurité Symfony)
- Gérer leur profil (avatar, compte)
- Consulter une base de figures communautaire

## 🚀 Stack technique
- PHP 8.2
- Symfony 6.4 LTS
- Doctrine ORM
- MySQL
- Bootstrap 5
- AssetMapper (Importmap)
- Twig
- PHPUnit

## ⚙ Installation du projet
### 1. Cloner le projet
<!-- bash -->
git clone <repo-url>
cd snowtricks
2. Installer les dépendances
composer install
3. Configurer l’environnement
cp .env .env.local

### Configurer la base de données :
DATABASE_URL="mysql://root:@127.0.0.1:3306/snowtricks_db"
4. Créer la base de données
php bin/console doctrine:database:create
5. Migrer la base
php bin/console doctrine:migrations:migrate
6. Charger les fixtures
php bin/console doctrine:fixtures:load
7. Compiler les assets
php bin/console asset-map:compile
8. Lancer le serveur
symfony server:start

### 🧪Tests
php bin/phpunit

### 👤 Comptes de test
Les utilisateurs de test sont disponibles dans :
src/DataFixtures/UserFixtures.php

### 🏗 Architecture
MVC Symfony
Service Layer
Repository Pattern
Security Voters (ACL)
Form Types
Event Subscribers (timestamps, login tracking)

### 📁 Structure du projet
SNOWTRICKS/
├── assets/
│   ├── styles/
│   ├── app.js
│
├── public/
│   ├── images/
│   ├── uploads/
│   │   ├── avatars/
│   │   ├── tricks/
│   │   │   └── fixtures/ (images de test)
│
├── src/
│   ├── Controller/
│   ├── DataFixtures/
│   ├── Entity/
│   ├── EventSubscriber/
│   ├── Form/
│   ├── Repository/
│   ├── Security/
│   ├── Service/
│   └── Templates/
│
└── tests/

### 🧭 Fonctionnalités principales
Authentification utilisateur
CRUD tricks
Upload images & vidéos
Commentaires avec permissions
Profil utilisateur
Sitemap SEO
Gestion des droits (Voters Symfony)

### 📈 Roadmap
v1.0.0 : MVP complet ✔
v1.1.0 : Amélioration UX/UI
v1.2.0 : Notifications + AJAX comments
