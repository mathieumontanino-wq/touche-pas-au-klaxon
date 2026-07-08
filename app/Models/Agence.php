<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

/**
 * Modèle d'accès aux données pour les agences (villes de l'entreprise).
 *
 * Toutes les requêtes utilisent des instructions préparées PDO afin de
 * prévenir les injections SQL.
 */
final class Agence
{
    private PDO $pdo;

    /**
     * @param PDO|null $pdo Connexion PDO à utiliser (par défaut : singleton applicatif).
     *                      Permet l'injection d'une connexion de test (SQLite en mémoire).
     */
    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::getConnection();
    }

    /**
     * Récupère toutes les agences, triées par nom de ville.
     *
     * @return array<int, array<string, mixed>>
     */
    public function findAll(): array
    {
        $pdo = $this->pdo;
        $stmt = $pdo->query('SELECT id_agence, nom_ville FROM agence ORDER BY nom_ville ASC');

        return $stmt->fetchAll();
    }

    /**
     * Récupère une agence par son identifiant.
     *
     * @param int $id Identifiant de l'agence.
     * @return array<string, mixed>|null
     */
    public function find(int $id): ?array
    {
        $pdo = $this->pdo;
        $stmt = $pdo->prepare('SELECT id_agence, nom_ville FROM agence WHERE id_agence = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch();

        return $result === false ? null : $result;
    }

    /**
     * Crée une nouvelle agence.
     *
     * @param string $nomVille Nom de la ville.
     * @return int Identifiant de l'agence créée.
     */
    public function create(string $nomVille): int
    {
        $pdo = $this->pdo;
        $stmt = $pdo->prepare('INSERT INTO agence (nom_ville) VALUES (:nom_ville)');
        $stmt->bindValue(':nom_ville', $nomVille);
        $stmt->execute();

        return (int) $pdo->lastInsertId();
    }

    /**
     * Met à jour le nom d'une agence existante.
     *
     * @param int    $id       Identifiant de l'agence.
     * @param string $nomVille Nouveau nom de la ville.
     * @return bool
     */
    public function update(int $id, string $nomVille): bool
    {
        $pdo = $this->pdo;
        $stmt = $pdo->prepare('UPDATE agence SET nom_ville = :nom_ville WHERE id_agence = :id');
        $stmt->bindValue(':nom_ville', $nomVille);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Supprime une agence.
     *
     * @param int $id Identifiant de l'agence.
     * @return bool
     */
    public function delete(int $id): bool
    {
        $pdo = $this->pdo;
        $stmt = $pdo->prepare('DELETE FROM agence WHERE id_agence = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Indique si une agence est encore référencée par au moins un trajet
     * (utile avant suppression, pour éviter une erreur de contrainte FK
     * brute et afficher un message métier clair).
     *
     * @param int $id Identifiant de l'agence.
     * @return bool
     */
    public function isUsedByTrajet(int $id): bool
    {
        $pdo = $this->pdo;
        $stmt = $pdo->prepare(
            'SELECT COUNT(*) FROM trajet WHERE id_agence_depart = :id1 OR id_agence_arrivee = :id2'
        );
        $stmt->bindValue(':id1', $id, PDO::PARAM_INT);
        $stmt->bindValue(':id2', $id, PDO::PARAM_INT);
        $stmt->execute();

        return ((int) $stmt->fetchColumn()) > 0;
    }
}
