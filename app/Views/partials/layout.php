<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Touche pas au klaxon : application de covoiturage inter-sites permettant aux employés de proposer et consulter des trajets partagés entre les agences de l'entreprise.">
    <title>Touche pas au klaxon</title>
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>

<?php require __DIR__ . '/header.php'; ?>

<main class="container my-4">
    <?php if ($flash !== null): ?>
        <div class="alert alert-<?= htmlspecialchars($flash['type'], ENT_QUOTES, 'UTF-8') ?>" role="alert">
            <?= htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <?= $content ?>
</main>

<?php require __DIR__ . '/footer.php'; ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>
