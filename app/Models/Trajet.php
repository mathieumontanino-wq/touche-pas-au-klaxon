<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

/**
 * Modèle d'accès aux données pour les trajets de covoiturage.
 *
 * Les méthodes de lecture renvoient des données enrichies (jointures avec
 * agence et utilisateur) afin de limiter les allers-retours en base côté
 * contrôleur.
 */
final class Trajet
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
     * Récupère les trajets futurs disposant d'au moins une place disponible,
     * triés par date de départ croissante (conforme à la page d'accueil).
     *
     * @return array<int, array<string, mixed>>
     */
    public function findDisponibles(): array
    {
        $pdo = $this->pdo;
        $sql = 'SELECT t.id_trajet, t.gdh_depart, t.gdh_arrivee, t.nb_places_total,
                       t.nb_places_disponibles, t.id_utilisateur,
                       ad.nom_ville AS ville_depart, aa.nom_ville AS ville_arrivee
                FROM trajet t
                INNER JOIN agence ad ON ad.id_agence = t.id_agence_depart
                INNER JOIN agence aa ON aa.id_agence = t.id_agence_arrivee
                WHERE t.nb_places_disponibles > 0
                  AND t.gdh_depart >= :maintenant
                ORDER BY t.gdh_depart ASC';

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':maintenant', date('Y-m-d H:i:s'));
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Récupère tous les trajets (vue administrateur, sans filtre de places
     * ni de date), triés par date de départ croissante.
     *
     * @return array<int, array<string, mixed>>
     */
    public function findAll(): array
    {
        $pdo = $this->pdo;
        $sql = 'SELECT t.id_trajet, t.gdh_depart, t.gdh_arrivee, t.nb_places_total,
                       t.nb_places_disponibles, t.id_utilisateur,
                       ad.nom_ville AS ville_depart, aa.nom_ville AS ville_arrivee,
                       u.nom AS auteur_nom, u.prenom AS auteur_prenom
                FROM trajet t
                INNER JOIN agence ad ON ad.id_agence = t.id_agence_depart
                INNER JOIN agence aa ON aa.id_agence = t.id_agence_arrivee
                INNER JOIN utilisateur u ON u.id_utilisateur = t.id_utilisateur
                ORDER BY t.gdh_depart ASC';

        return $pdo->query($sql)->fetchAll();
    }

    /**
     * Récupère le détail complet d'un trajet, incluant les coordonnées
     * de son auteur (nécessaire pour la fenêtre modale d'informations).
     *
     * @param int $id Identifiant du trajet.
     * @return array<string, mixed>|null
     */
    public function findWithDetails(int $id): ?array
    {
        $pdo = $this->pdo;
        $sql = 'SELECT t.id_trajet, t.gdh_depart, t.gdh_arrivee, t.nb_places_total,
                       t.nb_places_disponibles, t.id_agence_depart, t.id_agence_arrivee,
                       t.id_utilisateur,
                       ad.nom_ville AS ville_depart, aa.nom_ville AS ville_arrivee,
                       u.nom AS auteur_nom, u.prenom AS auteur_prenom,
                       u.email AS auteur_email, u.telephone AS auteur_telephone
                FROM trajet t
                INNER JOIN agence ad ON ad.id_agence = t.id_agence_depart
                INNER JOIN agence aa ON aa.id_agence = t.id_agence_arrivee
                INNER JOIN utilisateur u ON u.id_utilisateur = t.id_utilisateur
                WHERE t.id_trajet = :id';

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch();

        return $result === false ? null : $result;
    }

    /**
     * Crée un nouveau trajet.
     *
     * @param array<string, mixed> $data Données du trajet (clés : gdh_depart,
     *                                    gdh_arrivee, nb_places_total,
     *                                    nb_places_disponibles, id_agence_depart,
     *                                    id_agence_arrivee, id_utilisateur).
     * @return int Identifiant du trajet créé.
     */
    public function create(array $data): int
    {
        $pdo = $this->pdo;
        $sql = 'INSERT INTO trajet
                    (gdh_depart, gdh_arrivee, nb_places_total, nb_places_disponibles,
                     id_agence_depart, id_agence_arrivee, id_utilisateur)
                VALUES
                    (:gdh_depart, :gdh_arrivee, :nb_places_total, :nb_places_disponibles,
                     :id_agence_depart, :id_agence_arrivee, :id_utilisateur)';

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':gdh_depart'            => $data['gdh_depart'],
            ':gdh_arrivee'           => $data['gdh_arrivee'],
            ':nb_places_total'       => $data['nb_places_total'],
            ':nb_places_disponibles' => $data['nb_places_disponibles'],
            ':id_agence_depart'      => $data['id_agence_depart'],
            ':id_agence_arrivee'     => $data['id_agence_arrivee'],
            ':id_utilisateur'        => $data['id_utilisateur'],
        ]);

        return (int) $pdo->lastInsertId();
    }

    /**
     * Met à jour un trajet existant.
     *
     * @param int                   $id   Identifiant du trajet.
     * @param array<string, mixed>  $data Données à mettre à jour (mêmes clés que create()).
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $pdo = $this->pdo;
        $sql = 'UPDATE trajet SET
                    gdh_depart = :gdh_depart,
                    gdh_arrivee = :gdh_arrivee,
                    nb_places_total = :nb_places_total,
                    nb_places_disponibles = :nb_places_disponibles,
                    id_agence_depart = :id_agence_depart,
                    id_agence_arrivee = :id_agence_arrivee
                WHERE id_trajet = :id';

        $stmt = $pdo->prepare($sql);

        return $stmt->execute([
            ':gdh_depart'            => $data['gdh_depart'],
            ':gdh_arrivee'           => $data['gdh_arrivee'],
            ':nb_places_total'       => $data['nb_places_total'],
            ':nb_places_disponibles' => $data['nb_places_disponibles'],
            ':id_agence_depart'      => $data['id_agence_depart'],
            ':id_agence_arrivee'     => $data['id_agence_arrivee'],
            ':id'                    => $id,
        ]);
    }

    /**
     * Supprime un trajet.
     *
     * @param int $id Identifiant du trajet.
     * @return bool
     */
    public function delete(int $id): bool
    {
        $pdo = $this->pdo;
        $stmt = $pdo->prepare('DELETE FROM trajet WHERE id_trajet = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Indique si un utilisateur donné est bien l'auteur d'un trajet donné
     * (contrôle d'autorisation avant modification/suppression).
     *
     * @param int $idTrajet      Identifiant du trajet.
     * @param int $idUtilisateur Identifiant de l'utilisateur.
     * @return bool
     */
    public function isAuthor(int $idTrajet, int $idUtilisateur): bool
    {
        $pdo = $this->pdo;
        $stmt = $pdo->prepare('SELECT id_utilisateur FROM trajet WHERE id_trajet = :id');
        $stmt->bindValue(':id', $idTrajet, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetchColumn();

        return $result !== false && (int) $result === $idUtilisateur;
    }
}
