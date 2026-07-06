<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Utilisateur;

/**
 * Permet à l'administrateur de consulter la liste des utilisateurs.
 * Aucune opération de création, modification ou suppression n'est proposée,
 * conformément au cahier des charges (données issues du système RH).
 */
final class UtilisateurController extends Controller
{
    private Utilisateur $utilisateurModel;

    public function __construct()
    {
        $this->utilisateurModel = new Utilisateur();
    }

    /**
     * Affiche la liste de tous les utilisateurs.
     *
     * @return void
     */
    public function index(): void
    {
        $this->requireAdmin();

        $this->render('admin/utilisateurs', [
            'utilisateurs' => $this->utilisateurModel->findAll(),
        ]);
    }
}
