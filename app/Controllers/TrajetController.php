<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Session;
use App\Models\Agence;
use App\Models\Trajet;
use DateTime;
use Exception;

/**
 * Gère l'affichage et la gestion des trajets côté employé :
 * liste publique, détail, création, modification et suppression
 * (restreintes à l'auteur du trajet).
 */
final class TrajetController extends Controller
{
    private Trajet $trajetModel;
    private Agence $agenceModel;

    public function __construct()
    {
        $this->trajetModel = new Trajet();
        $this->agenceModel = new Agence();
    }

    /**
     * Page d'accueil : liste des trajets disponibles, triés par date de
     * départ croissante. Accessible à tous, connectés ou non.
     *
     * @return void
     */
    public function index(): void
    {
        $trajets = $this->trajetModel->findDisponibles();

        $this->render('trajet/index', [
            'trajets' => $trajets,
        ]);
    }

    /**
     * Affiche le détail d'un trajet (utilisé par la fenêtre modale côté
     * utilisateur connecté). Réservé aux utilisateurs authentifiés.
     *
     * @param int $id Identifiant du trajet.
     * @return void
     */
    public function show(int $id): void
    {
        $this->requireAuth();

        $trajet = $this->trajetModel->findWithDetails($id);

        if ($trajet === null) {
            $this->redirectWithFlash('danger', 'Ce trajet est introuvable.', '/');
        }

        $utilisateurCourant = Session::getUser();
        $estAuteur = $utilisateurCourant !== null && (int) $trajet['id_utilisateur'] === (int) $utilisateurCourant['id_utilisateur'];

        $this->render('trajet/show', [
            'trajet'    => $trajet,
            'estAuteur' => $estAuteur,
        ]);
    }

    /**
     * Affiche le formulaire de création d'un trajet.
     *
     * @return void
     */
    public function create(): void
    {
        $this->requireAuth();

        $this->render('trajet/create', [
            'agences'   => $this->agenceModel->findAll(),
            'csrfField' => Csrf::field(),
            'erreurs'   => [],
            'ancien'    => [],
        ]);
    }

    /**
     * Traite la soumission du formulaire de création de trajet.
     *
     * @return void
     */
    public function store(): void
    {
        $this->requireAuth();
        $this->requireValidCsrf();

        $utilisateurCourant = Session::getUser();
        $erreurs = $this->validateTrajetInput($_POST);

        if ($erreurs !== []) {
            $this->render('trajet/create', [
                'agences'   => $this->agenceModel->findAll(),
                'csrfField' => Csrf::field(),
                'erreurs'   => $erreurs,
                'ancien'    => $_POST,
            ]);

            return;
        }

        $this->trajetModel->create([
            'gdh_depart'            => $_POST['gdh_depart'],
            'gdh_arrivee'           => $_POST['gdh_arrivee'],
            'nb_places_total'       => (int) $_POST['nb_places_total'],
            'nb_places_disponibles' => (int) $_POST['nb_places_disponibles'],
            'id_agence_depart'      => (int) $_POST['id_agence_depart'],
            'id_agence_arrivee'     => (int) $_POST['id_agence_arrivee'],
            'id_utilisateur'        => (int) $utilisateurCourant['id_utilisateur'],
        ]);

        $this->redirectWithFlash('success', 'Le trajet a été créé avec succès.', '/');
    }

    /**
     * Affiche le formulaire de modification d'un trajet, réservé à son auteur.
     *
     * @param int $id Identifiant du trajet.
     * @return void
     */
    public function edit(int $id): void
    {
        $this->requireAuth();

        $trajet = $this->trajetModel->findWithDetails($id);
        $utilisateurCourant = Session::getUser();

        if ($trajet === null) {
            $this->redirectWithFlash('danger', 'Ce trajet est introuvable.', '/');
        }

        if ((int) $trajet['id_utilisateur'] !== (int) $utilisateurCourant['id_utilisateur']) {
            $this->redirectWithFlash('danger', 'Vous ne pouvez modifier que vos propres trajets.', '/');
        }

        $this->render('trajet/edit', [
            'trajet'    => $trajet,
            'agences'   => $this->agenceModel->findAll(),
            'csrfField' => Csrf::field(),
            'erreurs'   => [],
        ]);
    }

    /**
     * Traite la soumission du formulaire de modification de trajet.
     *
     * @param int $id Identifiant du trajet.
     * @return void
     */
    public function update(int $id): void
    {
        $this->requireAuth();
        $this->requireValidCsrf();

        $trajetExistant = $this->trajetModel->findWithDetails($id);
        $utilisateurCourant = Session::getUser();

        if ($trajetExistant === null) {
            $this->redirectWithFlash('danger', 'Ce trajet est introuvable.', '/');
        }

        if ((int) $trajetExistant['id_utilisateur'] !== (int) $utilisateurCourant['id_utilisateur']) {
            $this->redirectWithFlash('danger', 'Vous ne pouvez modifier que vos propres trajets.', '/');
        }

        $erreurs = $this->validateTrajetInput($_POST);

        if ($erreurs !== []) {
            $this->render('trajet/edit', [
                'trajet'    => array_merge($trajetExistant, $_POST),
                'agences'   => $this->agenceModel->findAll(),
                'csrfField' => Csrf::field(),
                'erreurs'   => $erreurs,
            ]);

            return;
        }

        $this->trajetModel->update($id, [
            'gdh_depart'            => $_POST['gdh_depart'],
            'gdh_arrivee'           => $_POST['gdh_arrivee'],
            'nb_places_total'       => (int) $_POST['nb_places_total'],
            'nb_places_disponibles' => (int) $_POST['nb_places_disponibles'],
            'id_agence_depart'      => (int) $_POST['id_agence_depart'],
            'id_agence_arrivee'     => (int) $_POST['id_agence_arrivee'],
        ]);

        $this->redirectWithFlash('success', 'Le trajet a été modifié avec succès.', '/');
    }

    /**
     * Supprime un trajet, réservé à son auteur.
     *
     * @param int $id Identifiant du trajet.
     * @return void
     */
    public function destroy(int $id): void
    {
        $this->requireAuth();
        $this->requireValidCsrf();

        $utilisateurCourant = Session::getUser();

        if (!$this->trajetModel->isAuthor($id, (int) $utilisateurCourant['id_utilisateur'])) {
            $this->redirectWithFlash('danger', 'Vous ne pouvez supprimer que vos propres trajets.', '/');
        }

        $this->trajetModel->delete($id);

        $this->redirectWithFlash('success', 'Le trajet a été supprimé avec succès.', '/');
    }

    /**
     * Valide les données saisies pour la création ou la modification d'un
     * trajet, en appliquant les contrôles de cohérence métier exigés par
     * le cahier des charges (agences différentes, arrivée après départ...).
     *
     * @param array<string, mixed> $input Données brutes issues de $_POST.
     * @return array<int, string> Liste des messages d'erreur (vide si valide).
     */
    private function validateTrajetInput(array $input): array
    {
        $erreurs = [];

        $gdhDepart  = $input['gdh_depart'] ?? '';
        $gdhArrivee = $input['gdh_arrivee'] ?? '';
        $idDepart   = $input['id_agence_depart'] ?? '';
        $idArrivee  = $input['id_agence_arrivee'] ?? '';
        $nbTotal    = $input['nb_places_total'] ?? '';
        $nbDispo    = $input['nb_places_disponibles'] ?? '';

        if ($gdhDepart === '' || $gdhArrivee === '') {
            $erreurs[] = 'Les dates de départ et d\'arrivée sont obligatoires.';
        }

        if (!ctype_digit((string) $idDepart) || !ctype_digit((string) $idArrivee)) {
            $erreurs[] = 'Veuillez sélectionner une agence de départ et une agence d\'arrivée valides.';
        } elseif ((int) $idDepart === (int) $idArrivee) {
            $erreurs[] = 'L\'agence de départ et l\'agence d\'arrivée doivent être différentes.';
        }

        if (!ctype_digit((string) $nbTotal) || (int) $nbTotal < 1) {
            $erreurs[] = 'Le nombre total de places doit être un entier positif.';
        }

        if (!ctype_digit((string) $nbDispo)) {
            $erreurs[] = 'Le nombre de places disponibles doit être un entier positif ou nul.';
        } elseif (ctype_digit((string) $nbTotal) && (int) $nbDispo > (int) $nbTotal) {
            $erreurs[] = 'Le nombre de places disponibles ne peut pas dépasser le nombre total de places.';
        }

        if ($erreurs === [] && $gdhDepart !== '' && $gdhArrivee !== '') {
            try {
                $depart = new DateTime($gdhDepart);
                $arrivee = new DateTime($gdhArrivee);

                if ($arrivee <= $depart) {
                    $erreurs[] = 'La date d\'arrivée doit être postérieure à la date de départ.';
                }
            } catch (Exception) {
                $erreurs[] = 'Le format des dates saisies est invalide.';
            }
        }

        return $erreurs;
    }
}
