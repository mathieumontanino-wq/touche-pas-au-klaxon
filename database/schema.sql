-- =============================================================
-- Touche pas au klaxon - Script de création du schéma physique
-- SGBD cible : MySQL / MariaDB
-- =============================================================

-- Suppression préalable (ordre inverse des dépendances) pour un script rejouable
DROP TABLE IF EXISTS trajet;
DROP TABLE IF EXISTS utilisateur;
DROP TABLE IF EXISTS agence;

-- -------------------------------------------------------------
-- Table AGENCE
-- Représente les villes/sites de l'entreprise.
-- Gérée exclusivement par l'administrateur.
-- -------------------------------------------------------------
CREATE TABLE agence (
    id_agence   INT UNSIGNED AUTO_INCREMENT,
    nom_ville   VARCHAR(100) NOT NULL,
    PRIMARY KEY (id_agence),
    UNIQUE KEY uk_agence_nom_ville (nom_ville)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table UTILISATEUR
-- Employés extraits du système RH (aucun CRUD applicatif dessus,
-- hormis l'authentification et la lecture).
-- -------------------------------------------------------------
CREATE TABLE utilisateur (
    id_utilisateur  INT UNSIGNED AUTO_INCREMENT,
    nom             VARCHAR(100) NOT NULL,
    prenom          VARCHAR(100) NOT NULL,
    email           VARCHAR(190) NOT NULL,
    telephone       VARCHAR(20)  NOT NULL,
    mot_de_passe    VARCHAR(255) NOT NULL COMMENT 'Hash du mot de passe (password_hash)',
    role            ENUM('employe', 'admin') NOT NULL DEFAULT 'employe',
    PRIMARY KEY (id_utilisateur),
    UNIQUE KEY uk_utilisateur_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table TRAJET
-- Trajets inter-sites proposés par les employés pour le covoiturage.
-- -------------------------------------------------------------
CREATE TABLE trajet (
    id_trajet               INT UNSIGNED AUTO_INCREMENT,
    gdh_depart              DATETIME NOT NULL COMMENT 'Groupe date-heure de départ',
    gdh_arrivee             DATETIME NOT NULL COMMENT 'Groupe date-heure d''arrivée',
    nb_places_total         TINYINT UNSIGNED NOT NULL,
    nb_places_disponibles   TINYINT UNSIGNED NOT NULL,
    id_agence_depart        INT UNSIGNED NOT NULL,
    id_agence_arrivee       INT UNSIGNED NOT NULL,
    id_utilisateur          INT UNSIGNED NOT NULL COMMENT 'Auteur du trajet',
    PRIMARY KEY (id_trajet),
    CONSTRAINT fk_trajet_agence_depart
        FOREIGN KEY (id_agence_depart) REFERENCES agence(id_agence)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_trajet_agence_arrivee
        FOREIGN KEY (id_agence_arrivee) REFERENCES agence(id_agence)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_trajet_utilisateur
        FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Contraintes de cohérence métier, ajoutées après création de la table
-- (évite les erreurs de validation MySQL 8.4+ sur les CHECK déclarés
-- dans le même bloc que les colonnes qu'ils référencent).
ALTER TABLE trajet
    ADD CONSTRAINT chk_trajet_places
        CHECK (nb_places_disponibles <= nb_places_total);

ALTER TABLE trajet
    ADD CONSTRAINT chk_trajet_agences_differentes
        CHECK (id_agence_depart <> id_agence_arrivee);


CREATE INDEX idx_trajet_gdh_depart ON trajet(gdh_depart);