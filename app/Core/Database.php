<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

/**
 * Gère l'unique connexion PDO à la base de données (patron Singleton).
 *
 * Garantit qu'une seule instance de connexion est créée pour toute la durée
 * de vie de la requête, et centralise la configuration PDO (mode d'erreur,
 * charset, mode de récupération par défaut).
 */
final class Database
{
    /**
     * @var Database|null Instance unique de la classe.
     */
    private static ?Database $instance = null;

    /**
     * @var PDO Connexion PDO active.
     */
    private PDO $connection;

    /**
     * Constructeur privé : empêche l'instanciation directe (cf. Singleton).
     *
     * @throws PDOException Si la connexion à la base de données échoue.
     */
    private function __construct()
    {
        $host    = $_ENV['DB_HOST'] ?? 'localhost';
        $port    = $_ENV['DB_PORT'] ?? '3306';
        $dbname  = $_ENV['DB_NAME'] ?? 'touche_pas_au_klaxon';
        $charset = 'utf8mb4';

        $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";

        $username = $_ENV['DB_USER'] ?? 'root';
        $password = $_ENV['DB_PASSWORD'] ?? '';

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        $this->connection = new PDO($dsn, $username, $password, $options);
    }

    /**
     * Empêche le clonage de l'instance unique.
     */
    private function __clone(): void
    {
    }

    /**
     * Retourne l'unique instance de connexion PDO, en la créant si nécessaire.
     *
     * @return PDO La connexion PDO active.
     */
    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance->connection;
    }
}
