<?php

declare(strict_types=1);

namespace App\Core;

use App\Controllers\AuthController;
use App\Controllers\TrajetController;
use App\Controllers\Admin\AgenceController;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\TrajetController as AdminTrajetController;
use App\Controllers\Admin\UtilisateurController;
use Buki\Router\Router as BukiRouter;

/**
 * Encapsule la librairie izniburak/router et centralise la déclaration
 * de toutes les routes de l'application, regroupées par niveau d'accès :
 * public, authentifié, et administrateur.
 *
 * Les contrôleurs sont instanciés explicitement via des fonctions anonymes,
 * ce qui garantit une résolution fiable des classes quel que soit leur
 * namespace (notamment le sous-namespace Admin).
 *
 * @see https://github.com/izniburak/php-router/wiki
 */
final class Router
{
    private BukiRouter $router;

    public function __construct()
    {
        $this->router = new BukiRouter();
    }

    /**
     * Déclare l'ensemble des routes de l'application.
     *
     * Les identifiants numériques dans l'URL utilisent le pattern intégré
     * :id (nombres naturels) fourni par la librairie izniburak/router.
     *
     * @return void
     */
    public function registerRoutes(): void
    {
        // --- Routes publiques ---
        $this->router->get('/', fn () => (new TrajetController())->index());
        $this->router->get('/connexion', fn () => (new AuthController())->showLogin());
        $this->router->post('/connexion', fn () => (new AuthController())->login());
        $this->router->get('/deconnexion', fn () => (new AuthController())->logout());

        // --- Routes authentifiées (employé connecté) ---
        $this->router->get('/trajets/creer', fn () => (new TrajetController())->create());
        $this->router->post('/trajets/creer', fn () => (new TrajetController())->store());
        $this->router->get('/trajets/:id', fn ($id) => (new TrajetController())->show((int) $id));
        $this->router->get('/trajets/:id/modifier', fn ($id) => (new TrajetController())->edit((int) $id));
        $this->router->post('/trajets/:id/modifier', fn ($id) => (new TrajetController())->update((int) $id));
        $this->router->post('/trajets/:id/supprimer', fn ($id) => (new TrajetController())->destroy((int) $id));

        // --- Routes administrateur ---
        $this->router->get('/admin', fn () => (new DashboardController())->index());
        $this->router->get('/admin/utilisateurs', fn () => (new UtilisateurController())->index());
        $this->router->get('/admin/agences', fn () => (new AgenceController())->index());
        $this->router->get('/admin/agences/creer', fn () => (new AgenceController())->create());
        $this->router->post('/admin/agences/creer', fn () => (new AgenceController())->store());
        $this->router->get('/admin/agences/:id/modifier', fn ($id) => (new AgenceController())->edit((int) $id));
        $this->router->post('/admin/agences/:id/modifier', fn ($id) => (new AgenceController())->update((int) $id));
        $this->router->post('/admin/agences/supprimer', fn () => (new AgenceController())->destroyPost());
        $this->router->get('/admin/trajets', fn () => (new AdminTrajetController())->index());
        $this->router->post('/admin/trajets/:id/supprimer', fn ($id) => (new AdminTrajetController())->destroy((int) $id));
    }

    /**
     * Exécute le routeur : résout la requête courante et appelle le
     * contrôleur correspondant.
     *
     * @return void
     */
    public function run(): void
    {
        $this->router->notFound(function () {
            http_response_code(404);
            require __DIR__ . '/../Views/errors/404.php';
        });

        $this->router->run();
    }
}
    
