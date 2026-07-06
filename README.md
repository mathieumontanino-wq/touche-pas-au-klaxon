# Touche pas au klaxon

Application de covoiturage inter-sites permettant aux employés de consulter,
proposer et gérer des trajets partagés entre les différentes agences de
l'entreprise.

## Sommaire

- [Fonctionnalités](#fonctionnalités)
- [Stack technique](#stack-technique)
- [Prérequis](#prérequis)
- [Installation](#installation)
- [Lancement de l'application](#lancement-de-lapplication)
- [Comptes de test](#comptes-de-test)
- [Tests unitaires](#tests-unitaires)
- [Qualité de code](#qualité-de-code)
- [Structure du projet](#structure-du-projet)
- [Modèle de données](#modèle-de-données)

## Fonctionnalités

- **Page d'accueil publique** : liste des trajets à venir disposant encore de
  places disponibles, triée par date de départ croissante.
- **Espace employé (authentifié)** : détail d'un trajet (contact, places
  totales), proposition d'un nouveau trajet, modification/suppression de ses
  propres trajets.
- **Espace administrateur** : tableau de bord, gestion complète des agences
  (CRUD), consultation des utilisateurs, consultation et suppression des
  trajets.

## Stack technique

- **PHP** 8.0+ (programmation orientée objet, architecture MVC)
- **MySQL / MariaDB** (accès via PDO, requêtes préparées)
- **izniburak/router** pour le routage HTTP
- **Bootstrap 5** + **Sass** pour l'interface (palette de couleurs imposée)
- **PHPUnit** pour les tests unitaires
- **PHPStan** pour l'analyse statique du code

## Prérequis

- PHP >= 8.0 avec les extensions `pdo_mysql`, `mbstring`, `json`
- MySQL ou MariaDB
- [Composer](https://getcomposer.org/)
- [Node.js](https://nodejs.org/) et npm (uniquement pour compiler le Sass)

## Installation

1. **Cloner le dépôt**

   ```bash
   git clone <url-du-depot>
   cd touche-pas-au-klaxon
   ```

2. **Installer les dépendances PHP**

   ```bash
   composer install
   ```

3. **Installer les dépendances front-end et compiler le CSS**

   ```bash
   npm install
   npm run build:css
   ```

4. **Configurer la base de données**

   Copier le fichier d'environnement d'exemple puis l'adapter :

   ```bash
   cp .env.example .env
   ```

   Modifier dans `.env` les identifiants de connexion à votre serveur MySQL local.

5. **Créer la base de données et son schéma**

   ```bash
   mysql -u root -p -e "CREATE DATABASE touche_pas_au_klaxon CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
   mysql -u root -p touche_pas_au_klaxon < database/schema.sql
   ```

6. **Charger le jeu d'essai**

   ```bash
   mysql -u root -p touche_pas_au_klaxon < database/seed.sql
   ```

## Lancement de l'application

En développement, le serveur intégré de PHP suffit :

```bash
php -S localhost:8000 -t public
```

L'application est alors accessible à l'adresse [http://localhost:8000](http://localhost:8000).

> En production, pointer le `DocumentRoot` du serveur web (Apache/Nginx) vers
> le dossier `public/`. Le fichier `.htaccess` fourni gère déjà la réécriture
> d'URL vers le front controller pour Apache.

## Comptes de test

| Rôle          | Email                        | Mot de passe |
|---------------|-------------------------------|---------------|
| Administrateur | `mathieu.admin@klaxon.fr`    | `password`    |
| Employé        | `julien.martin@klaxon.fr`    | `password`    |

D'autres comptes employés sont disponibles dans `database/seed.sql`, tous
avec le mot de passe `password`.

## Tests unitaires

Les tests couvrent en priorité les opérations d'écriture en base de données
(création, modification, suppression), ainsi que la protection CSRF. Ils
s'exécutent sur une base SQLite en mémoire (aucune configuration requise).

```bash
composer test
# équivalent à : vendor/bin/phpunit
```

## Qualité de code

Analyse statique du code avec PHPStan (niveau 6) :

```bash
composer analyse
# équivalent à : vendor/bin/phpstan analyse
```

## Structure du projet

```
├── app/
│   ├── Controllers/       Contrôleurs (Admin/ pour l'espace administrateur)
│   ├── Models/             Accès aux données (Agence, Utilisateur, Trajet)
│   ├── Views/              Vues PHP, organisées par domaine
│   └── Core/               Router, Database (singleton PDO), Controller
│                           abstrait, Session, Csrf
├── database/
│   ├── schema.sql          Script de création du schéma physique
│   └── seed.sql            Jeu d'essai (agences, utilisateurs, trajets)
├── public/
│   ├── index.php           Front controller
│   └── css/app.css         CSS compilé depuis scss/app.scss
├── scss/
│   └── app.scss            Sources Sass (surcharge des variables Bootstrap)
├── tests/                  Tests unitaires PHPUnit
├── .env.example            Modèle de configuration d'environnement
├── phpunit.xml             Configuration PHPUnit
└── phpstan.neon            Configuration PHPStan
```

## Modèle de données

### Modèle conceptuel (MCD)

- **AGENCE** (id_agence, nom_ville)
- **UTILISATEUR** (id_utilisateur, nom, prenom, email, telephone, mot_de_passe, role)
- **TRAJET** (id_trajet, gdh_depart, gdh_arrivee, nb_places_total, nb_places_disponibles)
  - un `TRAJET` a un auteur : relation vers `UTILISATEUR` (1,n)
  - un `TRAJET` a une agence de départ et une agence d'arrivée : deux relations vers `AGENCE` (1,n)

### Modèle logique (MLD)

```
AGENCE (id_agence, nom_ville)
    PK : id_agence

UTILISATEUR (id_utilisateur, nom, prenom, email, telephone, mot_de_passe, role)
    PK : id_utilisateur

TRAJET (id_trajet, gdh_depart, gdh_arrivee, nb_places_total, nb_places_disponibles,
        id_agence_depart, id_agence_arrivee, id_utilisateur)
    PK : id_trajet
    FK : id_agence_depart  → AGENCE(id_agence)
    FK : id_agence_arrivee → AGENCE(id_agence)
    FK : id_utilisateur    → UTILISATEUR(id_utilisateur)
```

Le schéma physique complet (types, contraintes CHECK, index) se trouve dans
`database/schema.sql`.

## Sécurité mise en œuvre

- Mots de passe hachés avec `password_hash()` / vérifiés avec `password_verify()`
- Toutes les requêtes SQL utilisent des instructions préparées PDO
- Protection CSRF sur tous les formulaires effectuant une opération d'écriture
- Échappement systématique des sorties HTML (`htmlspecialchars`)
- Contrôle d'accès par rôle (invité / employé / administrateur) au niveau de
  chaque contrôleur
- Validation des entrées côté serveur (formats, cohérence métier) avant toute
  écriture en base
