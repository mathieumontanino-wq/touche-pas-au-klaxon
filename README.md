# Touche pas au klaxon

Application web de covoiturage inter-sites permettant aux employés d'une
entreprise multi-sites de consulter, proposer et gérer des trajets partagés
entre les différentes agences (villes).

Projet réalisé dans le cadre du titre professionnel **Développeur Web**.

## Sommaire

- [Fonctionnalités](#fonctionnalités)
- [Stack technique](#stack-technique)
- [Prérequis](#prérequis)
- [Installation pas à pas](#installation-pas-à-pas)
- [Lancement de l'application](#lancement-de-lapplication)
- [Comptes de test](#comptes-de-test)
- [Tests unitaires](#tests-unitaires)
- [Qualité de code](#qualité-de-code)
- [Structure du projet](#structure-du-projet)
- [Modèle de données](#modèle-de-données)
- [Sécurité mise en œuvre](#sécurité-mise-en-œuvre)

## Fonctionnalités

- **Page d'accueil publique** : liste des trajets à venir disposant encore de
  places disponibles, triée par date de départ croissante. Les trajets passés
  ou complets ne sont pas affichés.
- **Espace employé (authentifié)** : consultation du détail d'un trajet
  (personne à contacter, téléphone, email, nombre total de places) via une
  fenêtre modale, proposition d'un nouveau trajet, modification et suppression
  de ses propres trajets.
- **Espace administrateur** : tableau de bord, gestion complète des agences
  (créer, modifier, supprimer), consultation de la liste des utilisateurs,
  consultation et suppression de tous les trajets.
- **Message flash** de confirmation après chaque opération d'écriture en base.

## Stack technique

- **PHP 8** (programmation orientée objet, architecture MVC, style défensif)
- **MySQL / MariaDB** (accès via PDO, requêtes préparées)
- **izniburak/router** pour le routage HTTP
- **Bootstrap 5** + **Sass** pour l'interface (palette de couleurs imposée)
- **PHPUnit** pour les tests unitaires
- **PHPStan** (niveau 6) pour l'analyse statique de la qualité du code

## Prérequis

- **PHP >= 8.0** avec les extensions suivantes activées dans `php.ini` :
  `pdo_mysql`, `mysqli`, `mbstring`, `openssl`, `fileinfo`, `zip`,
  et `pdo_sqlite` (nécessaire uniquement pour l'exécution des tests unitaires).
- **MySQL** ou **MariaDB**
- **Composer** (gestionnaire de dépendances PHP)
- **Node.js** et **npm** (uniquement pour compiler le CSS depuis les sources Sass)

## Installation pas à pas

### 1. Cloner le dépôt

```bash
git clone https://github.com/mathieumontanino-wq/touche-pas-au-klaxon.git
cd touche-pas-au-klaxon
```

### 2. Installer les dépendances PHP

```bash
composer install
```

### 3. Installer les dépendances front-end et compiler le CSS

```bash
npm install
npm run build:css
```

Cette étape génère le fichier `public/css/app.css` à partir des sources Sass
(`scss/app.scss`), en appliquant la palette de couleurs imposée.

### 4. Configurer la connexion à la base de données

Copier le fichier d'exemple puis l'adapter à votre environnement :

```bash
cp .env.example .env
```

Éditer le fichier `.env` et renseigner vos identifiants MySQL locaux,
notamment `DB_USER` et `DB_PASSWORD`.

> Le fichier `.env` contient des informations sensibles (mot de passe) et
> n'est volontairement pas versionné (voir `.gitignore`). Seul le modèle
> `.env.example` est fourni.

### 5. Créer la base de données

```bash
mysql -u root -p -e "CREATE DATABASE touche_pas_au_klaxon CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### 6. Créer les tables (schéma physique)

```bash
mysql -u root -p touche_pas_au_klaxon < database/schema.sql
```

### 7. Charger le jeu d'essai

```bash
mysql -u root -p touche_pas_au_klaxon < database/seed.sql
```

> Les étapes 5 à 7 peuvent également être réalisées graphiquement avec
> MySQL Workbench : créer le schéma, puis ouvrir et exécuter successivement
> `database/schema.sql` et `database/seed.sql`.

## Lancement de l'application

Depuis la racine du projet, démarrer le serveur de développement intégré de PHP
en précisant le script de routage `public/router.php` :

```bash
php -S localhost:8000 -t public public/router.php
```

L'application est alors accessible à l'adresse
[http://localhost:8000](http://localhost:8000).

> Le fichier `public/router.php` reproduit, pour le serveur intégré de PHP,
> la réécriture d'URL assurée par le fichier `.htaccess` en environnement
> Apache : les fichiers statiques existants (CSS...) sont servis directement,
> toutes les autres requêtes sont transmises au front controller
> `public/index.php`.
>
> En production sous Apache, il suffit de pointer le `DocumentRoot` vers le
> dossier `public/` ; le `.htaccess` fourni gère alors le routage.

## Comptes de test

| Rôle           | Email                       | Mot de passe |
|----------------|-----------------------------|--------------|
| Administrateur | `mathieu.admin@klaxon.fr`   | `password`   |
| Employé        | `julien.martin@klaxon.fr`   | `password`   |

Tous les comptes du jeu d'essai utilisent le mot de passe `password`
(stocké haché en base avec `password_hash()`).

## Tests unitaires

Les tests couvrent en priorité les opérations d'écriture en base de données
(création, modification, suppression) ainsi que la protection CSRF. Ils
s'exécutent sur une base SQLite en mémoire, sans aucune configuration
préalable ni accès à la base MySQL.

```bash
composer test
```

(équivalent à `vendor/bin/phpunit`)

## Qualité de code

Analyse statique du code avec PHPStan (niveau 6) :

```bash
composer analyse
```

(équivalent à `vendor/bin/phpstan analyse`)

## Structure du projet

```
touche-pas-au-klaxon/
├── app/
│   ├── Controllers/        Contrôleurs (sous-dossier Admin/ pour l'admin)
│   ├── Models/             Accès aux données (Agence, Utilisateur, Trajet)
│   ├── Views/              Vues PHP, organisées par domaine
│   └── Core/               Router, Database (singleton PDO), Controller
│                           abstrait, Session, Csrf
├── database/
│   ├── schema.sql          Script de création du schéma physique
│   └── seed.sql            Jeu d'essai (agences, utilisateurs, trajets)
├── public/
│   ├── index.php           Front controller (point d'entrée unique)
│   ├── router.php          Routage pour le serveur intégré de PHP
│   └── css/app.css         CSS compilé (généré par npm run build:css)
├── scss/
│   └── app.scss            Sources Sass (surcharge des variables Bootstrap)
├── tests/                  Tests unitaires PHPUnit
├── .env.example            Modèle de configuration d'environnement
├── .htaccess               Réécriture d'URL pour Apache (production)
├── composer.json           Dépendances PHP et scripts
├── package.json            Dépendances front-end et scripts de compilation CSS
├── phpstan.neon            Configuration PHPStan
└── phpunit.xml             Configuration PHPUnit
```

## Modèle de données

### Schéma conceptuel (MCD)

Trois entités :

- **AGENCE** (id_agence, nom_ville)
- **UTILISATEUR** (id_utilisateur, nom, prenom, email, telephone, mot_de_passe, role)
- **TRAJET** (id_trajet, gdh_depart, gdh_arrivee, nb_places_total, nb_places_disponibles)

Associations :

- Un `UTILISATEUR` **propose** de 0 à plusieurs `TRAJET` ; un `TRAJET` est
  proposé par exactement un `UTILISATEUR`.
- Une `AGENCE` est **agence de départ** de 0 à plusieurs `TRAJET` ; un `TRAJET`
  a exactement une agence de départ.
- Une `AGENCE` est **agence d'arrivée** de 0 à plusieurs `TRAJET` ; un `TRAJET`
  a exactement une agence d'arrivée.

### Schéma logique (MLD)

```
AGENCE (id_agence, nom_ville)
    Clé primaire : id_agence

UTILISATEUR (id_utilisateur, nom, prenom, email, telephone, mot_de_passe, role)
    Clé primaire : id_utilisateur

TRAJET (id_trajet, gdh_depart, gdh_arrivee, nb_places_total,
        nb_places_disponibles, #id_agence_depart, #id_agence_arrivee,
        #id_utilisateur)
    Clé primaire : id_trajet
    Clé étrangère : id_agence_depart  référence AGENCE(id_agence)
    Clé étrangère : id_agence_arrivee référence AGENCE(id_agence)
    Clé étrangère : id_utilisateur    référence UTILISATEUR(id_utilisateur)
```

Le schéma physique complet (types, contraintes, index) figure dans
`database/schema.sql`.

## Sécurité mise en œuvre

- Mots de passe hachés avec `password_hash()` et vérifiés avec `password_verify()`
- Toutes les requêtes SQL utilisent des instructions préparées PDO (protection
  contre les injections SQL)
- Protection CSRF sur tous les formulaires réalisant une opération d'écriture
- Échappement systématique des sorties HTML avec `htmlspecialchars()`
  (protection contre les failles XSS)
- Contrôle d'accès par rôle (visiteur / employé / administrateur) au niveau de
  chaque contrôleur
- Validation systématique des entrées côté serveur, avec contrôles de cohérence
  métier (agences de départ et d'arrivée différentes, date d'arrivée postérieure
  à la date de départ, nombre de places cohérent) avant toute écriture en base
- Régénération de l'identifiant de session à la connexion (protection contre la
  fixation de session)