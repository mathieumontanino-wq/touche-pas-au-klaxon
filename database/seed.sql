-- =============================================================
-- Touche pas au klaxon - Jeu d'essai
-- À exécuter après schema.sql
-- Mot de passe en clair pour TOUS les comptes de test : password
-- (haché ci-dessous avec password_hash() / bcrypt, jamais en clair)
-- =============================================================

-- -------------------------------------------------------------
-- AGENCES (10 villes)
-- -------------------------------------------------------------
INSERT INTO agence (nom_ville) VALUES
    ('Paris'),
    ('Lyon'),
    ('Marseille'),
    ('Toulouse'),
    ('Nantes'),
    ('Strasbourg'),
    ('Bordeaux'),
    ('Lille'),
    ('Rennes'),
    ('Nice');

-- -------------------------------------------------------------
-- UTILISATEURS
-- Hash bcrypt du mot de passe "password" pour tous les comptes.
-- id 1 = compte administrateur, les autres = employés.
-- -------------------------------------------------------------
INSERT INTO utilisateur (nom, prenom, email, telephone, mot_de_passe, role) VALUES
    ('Mathieu',  'Admin',     'mathieu.admin@klaxon.fr',  '0601020304', '$2b$10$NeGAhu27gYFoyzq6J7zucu2RRWn6ykuFAZbqUluz2GYCFHL1VGVm2', 'admin'),
    ('Martin',   'Julien',    'julien.martin@klaxon.fr',  '0611223344', '$2b$10$NeGAhu27gYFoyzq6J7zucu2RRWn6ykuFAZbqUluz2GYCFHL1VGVm2', 'employe'),
    ('Bernard',  'Camille',   'camille.bernard@klaxon.fr','0622334455', '$2b$10$NeGAhu27gYFoyzq6J7zucu2RRWn6ykuFAZbqUluz2GYCFHL1VGVm2', 'employe'),
    ('Petit',    'Nicolas',   'nicolas.petit@klaxon.fr',  '0633445566', '$2b$10$NeGAhu27gYFoyzq6J7zucu2RRWn6ykuFAZbqUluz2GYCFHL1VGVm2', 'employe'),
    ('Dubois',   'Manon',     'manon.dubois@klaxon.fr',   '0644556677', '$2b$10$NeGAhu27gYFoyzq6J7zucu2RRWn6ykuFAZbqUluz2GYCFHL1VGVm2', 'employe'),
    ('Moreau',   'Thomas',    'thomas.moreau@klaxon.fr',  '0655667788', '$2b$10$NeGAhu27gYFoyzq6J7zucu2RRWn6ykuFAZbqUluz2GYCFHL1VGVm2', 'employe'),
    ('Laurent',  'Emma',      'emma.laurent@klaxon.fr',   '0666778899', '$2b$10$NeGAhu27gYFoyzq6J7zucu2RRWn6ykuFAZbqUluz2GYCFHL1VGVm2', 'employe'),
    ('Simon',    'Lucas',     'lucas.simon@klaxon.fr',    '0677889900', '$2b$10$NeGAhu27gYFoyzq6J7zucu2RRWn6ykuFAZbqUluz2GYCFHL1VGVm2', 'employe');

-- -------------------------------------------------------------
-- TRAJETS
-- Mélange de trajets futurs (visibles sur la page d'accueil)
-- et un trajet passé (ne doit PAS apparaître sur la page d'accueil).
-- Dates volontairement fixées loin dans le futur pour rester valides longtemps.
-- -------------------------------------------------------------
INSERT INTO trajet (gdh_depart, gdh_arrivee, nb_places_total, nb_places_disponibles, id_agence_depart, id_agence_arrivee, id_utilisateur) VALUES
    ('2027-01-10 07:30:00', '2027-01-10 11:00:00', 4, 3, 1, 2, 2),  -- Paris -> Lyon, Julien
    ('2027-01-10 08:00:00', '2027-01-10 10:15:00', 3, 1, 2, 3, 3),  -- Lyon -> Marseille, Camille
    ('2027-01-11 06:45:00', '2027-01-11 09:30:00', 4, 4, 4, 1, 4),  -- Toulouse -> Paris, Nicolas
    ('2027-01-11 14:00:00', '2027-01-11 16:20:00', 3, 0, 5, 7, 5),  -- Nantes -> Bordeaux, complet (Manon)
    ('2027-01-12 09:00:00', '2027-01-12 11:45:00', 4, 2, 6, 1, 6),  -- Strasbourg -> Paris, Thomas
    ('2027-01-12 17:30:00', '2027-01-12 19:00:00', 3, 3, 8, 9, 7),  -- Lille -> Rennes, Emma
    ('2027-01-13 07:00:00', '2027-01-13 09:10:00', 4, 1, 10, 3, 8), -- Nice -> Marseille, Lucas
    ('2024-05-05 08:00:00', '2024-05-05 10:00:00', 4, 4, 1, 2, 2);  -- Trajet PASSÉ (ne doit pas apparaître à l'accueil)
