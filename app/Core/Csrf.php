<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Génère et valide les jetons CSRF (Cross-Site Request Forgery)
 * pour sécuriser les formulaires effectuant des opérations d'écriture.
 */
final class Csrf
{
    private const SESSION_KEY = 'csrf_token';

    /**
     * Génère (ou réutilise) un jeton CSRF stocké en session.
     *
     * @return string Le jeton CSRF courant.
     */
    public static function generateToken(): string
    {
        Session::start();

        if (empty($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::SESSION_KEY];
    }

    /**
     * Vérifie qu'un jeton CSRF soumis correspond à celui stocké en session.
     *
     * @param string|null $token Jeton soumis par le formulaire.
     * @return bool
     */
    public static function isValid(?string $token): bool
    {
        Session::start();

        if (!$token || empty($_SESSION[self::SESSION_KEY])) {
            return false;
        }

        return hash_equals($_SESSION[self::SESSION_KEY], $token);
    }

    /**
     * Génère un champ input caché HTML prêt à insérer dans un formulaire.
     *
     * @return string Balise HTML <input type="hidden">.
     */
    public static function field(): string
    {
        $token = self::generateToken();

        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }
}
