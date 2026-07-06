<h1 class="mb-4">Créer une agence</h1>

<?php if (!empty($erreurs)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($erreurs as $erreur): ?>
                <li><?= htmlspecialchars($erreur, ENT_QUOTES, 'UTF-8') ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form action="/admin/agences/creer" method="post" class="col-md-5">
    <?= $csrfField ?>

    <div class="mb-3">
        <label for="nom_ville" class="form-label">Nom de la ville</label>
        <input type="text" class="form-control" id="nom_ville" name="nom_ville" required maxlength="100"
               value="<?= htmlspecialchars($ancien['nom_ville'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    </div>

    <button type="submit" class="btn btn-primary">Créer</button>
</form>
