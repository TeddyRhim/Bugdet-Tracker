# Budget Tracker API

Une application Symfony 6 + API Platform permettant de gérer des utilisateurs, transactions et catégories.  
Elle expose des endpoints sécurisés pour suivre les finances personnelles et inclut des fonctionnalités avancées comme les alertes automatiques et des contrôles de sécurité stricts.

------------------------------------------------------------
Fonctionnalités
------------------------------------------------------------
- Gestion des utilisateurs
  - Création de compte (admin uniquement)
  - Récupération sécurisée du solde de son propre compte
  - Accès administrateur pour consulter/modifier tous les utilisateurs

- Gestion des transactions
  - Création de transactions (admin uniquement)
  - Consultation des 10 dernières transactions
  - Récupération des transactions de montant élevé (>1000€) avec alerte (log + email)
  - Duplication rapide d’une transaction

- Gestion des catégories
  - Récupération des transactions par catégorie
  - Calcul de totaux et de moyennes par catégorie

- Sécurité
  - Authentification par API Token
  - Rôles (ROLE_USER, ROLE_ADMIN)
  - Vérification que l’utilisateur connecté ne peut accéder qu’à ses propres données

- Automatisations (Lifecycle Callbacks)
  - Attribution automatique de la date de création (createdAt)
  - Validation des montants > 0
  - Hash du mot de passe utilisateur

- Événements (Event Subscribers)
  - Alerte en cas de transaction > 1000€ (log + possibilité d’envoi d’email via Mailer)
 
- Swagger
  - Simple Swagger généré par APIPlatform. (à corriger/modifier)

------------------------------------------------------------
Installation
------------------------------------------------------------
1. Cloner le projet :
   git clone https://github.com/ton-compte/budget-tracker.git
   cd budget-tracker

2. Installer les dépendances :
   composer install
   npm install && npm run build 

4. Configurer l’environnement (.env.local) :
   DATABASE_URL="mysql://root:password@127.0.0.1:3306/budget_tracker"
   MAILER_DSN=gmail://USERNAME:PASSWORD@default

5. Créer la base :
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate

6. Lancer le serveur :
   symfony serve
   -> API sur http://localhost:8000/api

------------------------------------------------------------
Authentification
------------------------------------------------------------
- Utilisation d’un API Token
- Header HTTP requis :
  Authorization: Bearer {API_TOKEN}

------------------------------------------------------------
Endpoints principaux
------------------------------------------------------------
- GET  /api/users/{id}                    -> Récupère un utilisateur (ROLE_USER)
- GET  /api/users/{id}/balance            -> Récupère le solde d’un utilisateur (Propriétaire/Admin)
- POST /api/users                         -> Créer un utilisateur (Propriétaire/Admin)

- GET  /api/transactions/{id}             -> Récupère une transaction par un id (ROLE_USER)
- GET  /api/transactions/recent           -> 10 dernières transactions (ROLE_USER)
- GET  /api/transactions/high             -> Transactions > 1000€ + alerte (ROLE_USER)
- POST /api/transactions                  -> Créer une transaction (Propriétaire/Admin)

- GET  /api/categories/{id}               -> Récupère une catégorie par un id (ROLE_USER)
- GET  /api/categories/{id}/total         -> Résumé des transactions d’une catégorie (ROLE_USER)
- GET  /api/categories/{id}/balance       -> Résumé des transactions (ROLE_USER)
- POST /api/categories                    -> Créer une categorie (Propriétaire/Admin)

------------------------------------------------------------
Développement
------------------------------------------------------------
Lifecycle Callbacks :
- Transaction::setCreatedAtValue() -> initialise createdAt
- Transaction::validateAmount() -> empêche montant <= 0
- User::hashPassword() -> hash automatique du mot de passe

Event Subscribers :
- TransactionHighSubscriber -> postPersist, log + email si montant > 1000€

------------------------------------------------------------
Alertes par email
------------------------------------------------------------
Configurer MAILER_DSN dans .env.local :
MAILER_DSN=gmail://USERNAME:PASSWORD@default (si gmail sinon utilisez : )

Tester l’envoi :
php bin/console messenger:consume async 
ou
test postman lors d'un POST d'une transactions

------------------------------------------------------------
TODO (Roadmap)
------------------------------------------------------------
- [x] CRUD Users
- [x] CRUD Transactions
- [x] Catégories + résumés
- [x] Endpoints avancés (recent, high, duplicate)
- [x] Lifecycle Callbacks (timestamps, validation, hash password)
- [x] Event Subscriber (alerte transaction > 1000)
- [x] Tests automatisés
- [x] Création et setup d'une VM pour Docker
- [x] Dockerisation
- [ ] Déploiement
