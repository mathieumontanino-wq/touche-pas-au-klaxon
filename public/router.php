<?php

declare(strict_types=1);

/**
 * Routeur pour le serveur de développement intégré de PHP (php -S).
 * Reproduit le comportement du .htaccess Apache : les fichiers statiques
 * existants sont servis directement, le reste passe par index.php.
 */

$requestedPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$absolutePath = __DIR__ . $requestedPath;

if ($requestedPath !== '/' && is_file($absolutePath)) {
    return false;
}

require __DIR__ . '/index.php';