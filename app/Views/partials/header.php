<?php
/**
 * En-tête de l'application.
 * Utilise $utilisateurConnecte, transmis automatiquement par Controller::render().
 *
 * @var array<string, mixed>|null $utilisateurConnecte
 */
$estAdmin = $utilisateurConnecte !== null && ($utilisateurConnecte['role'] ?? null) === 'admin';
?>
<header class="navbar navbar-expand-lg navbar-dark bg-primary px-3">
    <a class="navbar-brand" href="<?= $estAdmin ? '/admin' : '/' ?>">Touche pas au klaxon</a>

    <div class="d-flex align-items-center gap-3 ms-auto">
        <?php if ($utilisateurConnecte === null): ?>
            <a href="/connexion" class="btn btn-outline-light">Connexion</a>

        <?php elseif ($estAdmin): ?>
            <nav class="d-flex gap-3">
                <a href="/admin/utilisateurs" class="text-white">Utilisateurs</a>
                <a href="/admin/agences" class="text-white">Agences</a>
                <a href="/admin/trajets" class="text-white">Trajets</a>
            </nav>
            <form action="/deconnexion" method="get" class="mb-0">
                <button type="submit" class="btn btn-outline-light">Déconnexion</button>
            </form>

        <?php else: ?>
            <a href="/trajets/creer" class="btn btn-light">Proposer un trajet</a>
            <span class="text-white">
                <?= htmlspecialchars($utilisateurConnecte['prenom'] . ' ' . $utilisateurConnecte['nom'], ENT_QUOTES, 'UTF-8') ?>
            </span>
            <form action="/deconnexion" method="get" class="mb-0">
                <button type="submit" class="btn btn-outline-light">Déconnexion</button>
            </form>
        <?php endif; ?>
    </div>
</header>
