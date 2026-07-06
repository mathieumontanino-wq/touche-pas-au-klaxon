<?php

declare(strict_types=1);

namespace App\Core;

use Buki\Router\Router as BukiRouter;

/**
 * Encapsule la librairie izniburak/router et centralise la déclaration
 * de toutes les routes de l'application, regroupées par niveau d'accès :
 * public, authentifié, et administrateur.
 *
 * @see https://github.com/izniburak/php-router/wiki
 */
final class Router
{
    private BukiRouter $router;

    public function __construct()
    {
        $this->router = new BukiRouter([
            'paths' => [
                'controllers' => 'App\\Controllers',
            ],
            'namespaces' => [
                'controllers' => 'App\\Controllers',
            ],
        ]);
    }

    /**
     * Déclare l'ensemble des routes de l'application.
     *
     * Les identifiants numériques dans l'URL utilisent le pattern intégré
     * `{i}` (entier), conformément à la syntaxe de izniburak/router.
     *
     * @return void
     */
    public function registerRoutes(): void
    {
        // --- Routes publiques (accessibles sans connexion) ---
        $this->router->get('/', 'TrajetController@index');
        $this->router->get('/connexion', 'AuthController@showLogin');
        $this->router->post('/connexion', 'AuthController@login');
        $this->router->get('/deconnexion', 'AuthController@logout');

        // --- Routes authentifiées (employé connecté) ---
        $this->router->get('/trajets/creer', 'TrajetController@create');
        $this->router->post('/trajets/creer', 'TrajetController@store');
        $this->router->get('/trajets/{i}', 'TrajetController@show');
        $this->router->get('/trajets/{i}/modifier', 'TrajetController@edit');
        $this->router->post('/trajets/{i}/modifier', 'TrajetController@update');
        $this->router->post('/trajets/{i}/supprimer', 'TrajetController@destroy');

        // --- Routes administrateur ---
        $this->router->get('/admin', 'Admin.DashboardController@index');
        $this->router->get('/admin/utilisateurs', 'Admin.UtilisateurController@index');
        $this->router->get('/admin/agences', 'Admin.AgenceController@index');
        $this->router->get('/admin/agences/creer', 'Admin.AgenceController@create');
        $this->router->post('/admin/agences/creer', 'Admin.AgenceController@store');
        $this->router->get('/admin/agences/{i}/modifier', 'Admin.AgenceController@edit');
        $this->router->post('/admin/agences/{i}/modifier', 'Admin.AgenceController@update');
        $this->router->post('/admin/agences/{i}/supprimer', 'Admin.AgenceController@destroy');
        $this->router->get('/admin/trajets', 'Admin.TrajetController@index');
        $this->router->post('/admin/trajets/{i}/supprimer', 'Admin.TrajetController@destroy');
    }

    /**
     * Exécute le routeur : résout la requête courante et appelle le
     * contrôleur correspondant.
     *
     * @return void
     */
    public function run(): void
    {
        // Handler de route non trouvée : affiche l'URI pour diagnostic
        $this->router->notFound(function () {
            http_response_code(404);
            echo '<pre style="background:#fff;color:#000;padding:20px;font-size:14px;">';
            echo "ROUTE NON TROUVEE (404)\n";
            echo 'URI demandee : ' . htmlspecialchars($_SERVER['REQUEST_URI'] ?? '?') . "\n";
            echo 'Methode : ' . htmlspecialchars($_SERVER['REQUEST_METHOD'] ?? '?') . "\n";
            echo '</pre>';
        });

        // Handler d'erreur interne : affiche l'exception reelle
        $this->router->error(function ($request, $response, $exception = null) {
            http_response_code(500);
            echo '<pre style="background:#fff;color:#000;padding:20px;font-size:14px;">';
            echo "ERREUR INTERNE DU ROUTEUR (500)\n\n";
            if ($exception instanceof \Throwable) {
                echo 'Message : ' . $exception->getMessage() . "\n\n";
                echo 'Fichier : ' . $exception->getFile() . ':' . $exception->getLine() . "\n\n";
                echo $exception->getTraceAsString();
            } else {
                echo "Aucun detail d'exception disponible.\n";
                echo 'URI : ' . htmlspecialchars($_SERVER['REQUEST_URI'] ?? '?') . "\n";
            }
            echo '</pre>';
        });

        $this->router->run();
    }
}