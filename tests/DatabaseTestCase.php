<?php

declare(strict_types=1);

namespace Tests;

use PDO;
use PHPUnit\Framework\TestCase;

/**
 * Classe de base pour les tests nécessitant un accès à la base de données.
 *
 * Utilise une base SQLite en mémoire plutôt qu'une vraie instance MySQL,
 * afin que les tests soient rapides, isolés et exécutables sans
 * configuration d'environnement (CI/CD, machine de correction...).
 * Le schéma est volontairement simplifié mais respecte les mêmes
 * contraintes de clés primaires et étrangères que le schéma MySQL réel.
 */
abstract class DatabaseTestCase extends TestCase
{
    protected PDO $pdo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->pdo->exec('PRAGMA foreign_keys = ON');

        $this->pdo->exec('
            CREATE TABLE agence (
                id_agence INTEGER PRIMARY KEY AUTOINCREMENT,
                nom_ville VARCHAR(100) NOT NULL UNIQUE
            )
        ');

        $this->pdo->exec('
            CREATE TABLE utilisateur (
                id_utilisateur INTEGER PRIMARY KEY AUTOINCREMENT,
                nom VARCHAR(100) NOT NULL,
                prenom VARCHAR(100) NOT NULL,
                email VARCHAR(190) NOT NULL UNIQUE,
                telephone VARCHAR(20) NOT NULL,
                mot_de_passe VARCHAR(255) NOT NULL,
                role VARCHAR(10) NOT NULL DEFAULT "employe"
            )
        ');

        $this->pdo->exec('
            CREATE TABLE trajet (
                id_trajet INTEGER PRIMARY KEY AUTOINCREMENT,
                gdh_depart DATETIME NOT NULL,
                gdh_arrivee DATETIME NOT NULL,
                nb_places_total INTEGER NOT NULL,
                nb_places_disponibles INTEGER NOT NULL,
                id_agence_depart INTEGER NOT NULL REFERENCES agence(id_agence),
                id_agence_arrivee INTEGER NOT NULL REFERENCES agence(id_agence),
                id_utilisateur INTEGER NOT NULL REFERENCES utilisateur(id_utilisateur)
            )
        ');
    }
}
