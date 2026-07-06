<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;

/**
 * Tableau de bord de l'administrateur : point d'entrée après connexion admin,
 * donnant accès aux différentes fonctionnalités de gestion.
 */
final class DashboardController extends Controller
{
    /**
     * Affiche le tableau de bord administrateur.
     *
     * @return void
     */
    public function index(): void
    {
        $this->requireAdmin();

        $this->render('admin/dashboard');
    }
}
