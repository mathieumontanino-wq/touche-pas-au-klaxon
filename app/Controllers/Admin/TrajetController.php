<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Trajet;

/**
 * Permet à l'administrateur de consulter l'ensemble des trajets et
 * d'en supprimer, conformément au cahier des charges (l'administrateur
 * ne crée ni ne modifie de trajet, il peut uniquement lister et supprimer).
 */
final class TrajetController extends Controller
{
    private Trajet $trajetModel;

    public function __construct()
    {
        $this->trajetModel = new Trajet();
    }

    /**
     * Affiche la liste de tous les trajets.
     *
     * @return void
     */
    public function index(): void
    {
        $this->requireAdmin();

        $this->render('admin/trajets', [
            'trajets' => $this->trajetModel->findAll(),
        ]);
    }

    /**
     * Supprime un trajet.
     *
     * @param int $id Identifiant du trajet.
     * @return void
     */
    public function destroy(int $id): void
    {
        $this->requireAdmin();
        $this->requireValidCsrf();

        $this->trajetModel->delete($id);

        $this->redirectWithFlash('success', 'Le trajet a été supprimé avec succès.', '/admin/trajets');
    }
}
