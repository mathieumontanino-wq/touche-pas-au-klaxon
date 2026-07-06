<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Encapsule la gestion de la session PHP native.
 *
 * Centralise le démarrage de session, l'authentification de l'utilisateur
 * courant et les messages flash (affichés une seule fois après une
 * opération d'écriture, conformément au cahier des charges).
 */
final class Session
{
    /**
     * Démarre la session PHP si elle n'est pas déjà active.
     *
     * @return void
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Enregistre l'utilisateur authentifié en session.
     *
     * @param array<string, mixed> $utilisateur Données de l'utilisateur connecté.
     * @return void
     */
    public static function login(array $utilisateur): void
    {
        self::start();
        session_regenerate_id(true);
        $_SESSION['utilisateur'] = $utilisateur;
    }

    /**
     * Déconnecte l'utilisateur courant et détruit la session.
     *
     * @return void
     */
    public static function logout(): void
    {
        self::start();
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }

    /**
     * Indique si un utilisateur est actuellement authentifié.
     *
     * @return bool
     */
    public static function isAuthenticated(): bool
    {
        self::start();

        return isset($_SESSION['utilisateur']);
    }

    /**
     * Indique si l'utilisateur authentifié possède le rôle administrateur.
     *
     * @return bool
     */
    public static function isAdmin(): bool
    {
        self::start();

        return self::isAuthenticated() && ($_SESSION['utilisateur']['role'] ?? null) === 'admin';
    }

    /**
     * Retourne les données de l'utilisateur actuellement connecté.
     *
     * @return array<string, mixed>|null
     */
    public static function getUser(): ?array
    {
        self::start();

        return $_SESSION['utilisateur'] ?? null;
    }

    /**
     * Dépose un message flash à afficher une seule fois (page suivante).
     *
     * @param string $type    Type de message (success, danger, warning, info).
     * @param string $message Contenu du message.
     * @return void
     */
    public static function setFlash(string $type, string $message): void
    {
        self::start();
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    /**
     * Récupère puis efface le message flash en attente, s'il existe.
     *
     * @return array{type: string, message: string}|null
     */
    public static function getFlash(): ?array
    {
        self::start();

        if (!isset($_SESSION['flash'])) {
            return null;
        }

        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);

        return $flash;
    }
}
