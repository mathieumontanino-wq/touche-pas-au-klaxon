<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Classe abstraite dont héritent tous les contrôleurs de l'application.
 *
 * Fournit les méthodes transverses : rendu de vues avec layout, redirections,
 * et guards d'accès (authentification / rôle administrateur).
 */
abstract class Controller
{
    /**
     * Chemin absolu vers le dossier des vues.
     */
    private const VIEWS_PATH = __DIR__ . '/../Views/';

    /**
     * Affiche une vue en l'insérant dans le layout principal.
     *
     * @param string               $view Chemin relatif de la vue (sans extension), ex: "trajet/index".
     * @param array<string, mixed> $data Données à extraire dans le scope de la vue.
     * @return void
     */
    protected function render(string $view, array $data = []): void
    {
        $viewFile = self::VIEWS_PATH . $view . '.php';

        if (!is_file($viewFile)) {
            throw new \RuntimeException(sprintf('Vue introuvable : %s', $viewFile));
        }

        extract($data, EXTR_SKIP);

        $flash = Session::getFlash();
        $utilisateurConnecte = Session::getUser();

        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        require self::VIEWS_PATH . 'partials/layout.php';
    }

    /**
     * Redirige l'utilisateur vers une autre URL et termine le script.
     *
     * @param string $url Chemin de destination (ex : "/trajets").
     * @return never
     */
    protected function redirect(string $url): void
    {
        if (ob_get_level() > 0) {
            ob_end_clean();
        }
        header('Location: ' . $url);
        exit(0);
    }

    /**
     * Dépose un message flash puis redirige (raccourci pour le pattern
     * "écriture en base -> redirection -> message de confirmation").
     *
     * @param string $type    Type du message (success, danger, warning, info).
     * @param string $message Contenu du message affiché à l'utilisateur.
     * @param string $url     URL de redirection.
     * @return never
     */
    protected function redirectWithFlash(string $type, string $message, string $url): void
    {
        Session::setFlash($type, $message);
        $this->redirect($url);
    }

    /**
     * Vérifie que l'utilisateur est authentifié, sinon redirige vers la connexion.
     *
     * @return void
     */
    protected function requireAuth(): void
    {
        if (!Session::isAuthenticated()) {
            $this->redirectWithFlash('warning', 'Vous devez être connecté pour accéder à cette page.', '/connexion');
        }
    }

    /**
     * Vérifie que l'utilisateur est administrateur, sinon redirige.
     *
     * @return void
     */
    protected function requireAdmin(): void
    {
        $this->requireAuth();

        if (!Session::isAdmin()) {
            $this->redirectWithFlash('danger', 'Accès réservé aux administrateurs.', '/');
        }
    }

    /**
     * Valide le jeton CSRF d'une requête POST, sinon interrompt le traitement.
     *
     * @return void
     */
    protected function requireValidCsrf(): void
    {
        $token = $_POST['csrf_token'] ?? null;

        if (!Csrf::isValid($token)) {
            $this->redirectWithFlash('danger', 'Requête invalide (jeton de sécurité expiré). Veuillez réessayer.', '/');
        }
    }
}
