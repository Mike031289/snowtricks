# 🧠 2. DOSSIER TECHNIQUE (IMPORTANT POUR OC)
Tu peux le mettre en `TECHNICAL.md`

## 📘 SnowTricks - Dossier Technique
### 🧩 Architecture globale
Le projet suit l’architecture MVC de Symfony :

- **Controller** : gestion des requêtes HTTP
- **Entity** : modèle de données Doctrine
- **Repository** : requêtes base de données
- **Service** : logique métier (upload media, avatar)
- **FormType** : gestion des formulaires

### 🔐 Sécurité
- Authentification Symfony Security
- Voters :
  - `COMMENT_EDIT`
  - `COMMENT_DELETE`
  - `TRICK_EDIT`
- Protection CSRF sur toutes les actions sensibles
- Gestion des rôles utilisateur

### 🧠 Logique métier
- Upload images via `MediaService`
- Upload avatars via `AvatarService`
- Gestion des vidéos (YouTube / Dailymotion embed)
- Slug automatique via Symfony String component

### 📊 Base de données
Relations principales :
- User → Tricks (OneToMany)
- Trick → Comments (OneToMany)
- Trick → Images (OneToMany)
- Trick → Videos (OneToMany)
- Trick → Group (ManyToOne)

### ⚙ Event Subscribers
- Mise à jour automatique des timestamps
- Tracking login utilisateur (lastLoginAt)
- Gestion des événements Doctrine

### 🔎 SEO
- Sitemap XML dynamique
- URLs SEO friendly :

/trick/{id}-{slug}

- Robots.txt
- APP_URL configurable (.env)

### 🎨 Frontend
- Bootstrap 5
- Importmap (AssetMapper Symfony)
- Responsive design
- UI simple orientée UX

### 🧪 Tests
- PHPUnit utilisé pour tests fonctionnels
- Fixtures pour environnement de test
