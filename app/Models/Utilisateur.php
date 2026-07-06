<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

/**
 * Modèle d'accès aux données pour les utilisateurs (employés).
 *
 * Conformément au cahier des charges, ce modèle n'expose aucune méthode
 * de création, modification ou suppression : les utilisateurs proviennent
 * du système RH et sont uniquement consultés ou authentifiés.
 */
final class Utilisateur
{
    private PDO $pdo;

    /**
     * @param PDO|null $pdo Connexion PDO à utiliser (par défaut : singleton applicatif).
     */
    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::getConnection();
    }

    /**
     * Récupère tous les utilisateurs, triés par nom puis prénom.
     *
     * @return array<int, array<string, mixed>>
     */
    public function findAll(): array
    {
        $pdo = $this->pdo;
        $stmt = $pdo->query(
            'SELECT id_utilisateur, nom, prenom, email, telephone, role
             FROM utilisateur ORDER BY nom ASC, prenom ASC'
        );

        return $stmt->fetchAll();
    }

    /**
     * Récupère un utilisateur par son identifiant.
     *
     * @param int $id Identifiant de l'utilisateur.
     * @return array<string, mixed>|null
     */
    public function find(int $id): ?array
    {
        $pdo = $this->pdo;
        $stmt = $pdo->prepare(
            'SELECT id_utilisateur, nom, prenom, email, telephone, role
             FROM utilisateur WHERE id_utilisateur = :id'
        );
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch();

        return $result === false ? null : $result;
    }

    /**
     * Récupère un utilisateur par son adresse email, avec son mot de passe
     * haché (nécessaire uniquement pour la vérification d'authentification).
     *
     * @param string $email Adresse email saisie lors de la connexion.
     * @return array<string, mixed>|null
     */
    public function findByEmailWithPassword(string $email): ?array
    {
        $pdo = $this->pdo;
        $stmt = $pdo->prepare(
            'SELECT id_utilisateur, nom, prenom, email, telephone, mot_de_passe, role
             FROM utilisateur WHERE email = :email'
        );
        $stmt->bindValue(':email', $email);
        $stmt->execute();

        $result = $stmt->fetch();

        return $result === false ? null : $result;
    }
}
