<h1 class="mb-4">Connexion</h1>

<?php if ($erreur !== null): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($erreur, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<form action="/connexion" method="post" class="col-md-5">
    <?= $csrfField ?>

    <div class="mb-3">
        <label for="email" class="form-label">Adresse email</label>
        <input type="email" class="form-control" id="email" name="email" required
               value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    </div>

    <div class="mb-3">
        <label for="mot_de_passe" class="form-label">Mot de passe</label>
        <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" required>
    </div>

    <button type="submit" class="btn btn-primary">Se connecter</button>
</form>
