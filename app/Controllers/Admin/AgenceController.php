<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Csrf;
use App\Models\Agence;

/**
 * Gère le CRUD complet des agences, réservé à l'administrateur
 * (seul rôle autorisé à créer, modifier ou supprimer une agence).
 */
final class AgenceController extends Controller
{
    private Agence $agenceModel;

    public function __construct()
    {
        $this->agenceModel = new Agence();
    }

    /**
     * Affiche la liste des agences.
     *
     * @return void
     */
    public function index(): void
    {
        $this->requireAdmin();

        $this->render('admin/agences/index', [
            'agences' => $this->agenceModel->findAll(),
        ]);
    }

    /**
     * Affiche le formulaire de création d'une agence.
     *
     * @return void
     */
    public function create(): void
    {
        $this->requireAdmin();

        $this->render('admin/agences/create', [
            'csrfField' => Csrf::field(),
            'erreurs'   => [],
            'ancien'    => [],
        ]);
    }

    /**
     * Traite la création d'une agence.
     *
     * @return void
     */
    public function store(): void
    {
        $this->requireAdmin();
        $this->requireValidCsrf();

        $nomVille = trim((string) ($_POST['nom_ville'] ?? ''));
        $erreurs = $this->validateNomVille($nomVille);

        if ($erreurs !== []) {
            $this->render('admin/agences/create', [
                'csrfField' => Csrf::field(),
                'erreurs'   => $erreurs,
                'ancien'    => $_POST,
            ]);

            return;
        }

        $this->agenceModel->create($nomVille);

        $this->redirectWithFlash('success', 'L\'agence a été créée avec succès.', '/admin/agences');
    }

    /**
     * Affiche le formulaire de modification d'une agence.
     *
     * @param int $id Identifiant de l'agence.
     * @return void
     */
    public function edit(int $id): void
    {
        $this->requireAdmin();

        $agence = $this->agenceModel->find($id);

        if ($agence === null) {
            $this->redirectWithFlash('danger', 'Cette agence est introuvable.', '/admin/agences');
        }

        $this->render('admin/agences/edit', [
            'agence'    => $agence,
            'csrfField' => Csrf::field(),
            'erreurs'   => [],
        ]);
    }

    /**
     * Traite la modification d'une agence.
     *
     * @param int $id Identifiant de l'agence.
     * @return void
     */
    public function update(int $id): void
    {
        $this->requireAdmin();
        $this->requireValidCsrf();

        $agence = $this->agenceModel->find($id);

        if ($agence === null) {
            $this->redirectWithFlash('danger', 'Cette agence est introuvable.', '/admin/agences');
        }

        $nomVille = trim((string) ($_POST['nom_ville'] ?? ''));
        $erreurs = $this->validateNomVille($nomVille);

        if ($erreurs !== []) {
            $this->render('admin/agences/edit', [
                'agence'    => array_merge($agence, ['nom_ville' => $nomVille]),
                'csrfField' => Csrf::field(),
                'erreurs'   => $erreurs,
            ]);

            return;
        }

        $this->agenceModel->update($id, $nomVille);

        $this->redirectWithFlash('success', 'L\'agence a été modifiée avec succès.', '/admin/agences');
    }

    /**
     * Traite la suppression d'agence via formulaire POST sans paramètre URL.
     *
     * @return void
     */
    public function destroyPost(): void
    {
        $this->requireAdmin();
        $this->requireValidCsrf();

        $id = (int) ($_POST['agence_id'] ?? 0);

        if ($id === 0) {
            $this->redirectWithFlash('danger', 'Identifiant invalide.', '/admin/agences');
        }

        if ($this->agenceModel->isUsedByTrajet($id)) {
            $this->redirectWithFlash(
                'danger',
                'Impossible de supprimer cette agence : elle est utilisée par au moins un trajet.',
                '/admin/agences'
            );
        }

        $this->agenceModel->delete($id);

        $this->redirectWithFlash('success', 'L\'agence a été supprimée avec succès.', '/admin/agences');
    }

    /**
     * Supprime une agence (méthode conservée pour compatibilité).
     *
     * @param int $id Identifiant de l'agence.
     * @return void
     */
    public function destroy(int $id): void
    {
        $this->destroyPost();
    }

    /**
     * Valide le nom de ville saisi pour une agence.
     *
     * @param string $nomVille Nom de ville saisi.
     * @return array<int, string> Liste des messages d'erreur (vide si valide).
     */
    private function validateNomVille(string $nomVille): array
    {
        $erreurs = [];

        if ($nomVille === '') {
            $erreurs[] = 'Le nom de la ville est obligatoire.';
        } elseif (mb_strlen($nomVille) > 100) {
            $erreurs[] = 'Le nom de la ville ne doit pas dépasser 100 caractères.';
        }

        return $erreurs;
    }
}
