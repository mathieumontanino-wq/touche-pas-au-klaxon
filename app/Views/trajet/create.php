<h1 class="mb-4">Proposer un trajet</h1>

<?php if (!empty($erreurs)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($erreurs as $erreur): ?>
                <li><?= htmlspecialchars($erreur, ENT_QUOTES, 'UTF-8') ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form action="/trajets/creer" method="post" class="col-md-6">
    <?= $csrfField ?>

    <fieldset class="mb-3" disabled>
        <legend class="h6">Vos coordonnées (non modifiables)</legend>
        <div class="row">
            <div class="col">
                <label class="form-label">Nom</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($utilisateurConnecte['nom'], ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="col">
                <label class="form-label">Prénom</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($utilisateurConnecte['prenom'], ENT_QUOTES, 'UTF-8') ?>">
            </div>
        </div>
        <div class="row mt-2">
            <div class="col">
                <label class="form-label">Email</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($utilisateurConnecte['email'], ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="col">
                <label class="form-label">Téléphone</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($utilisateurConnecte['telephone'], ENT_QUOTES, 'UTF-8') ?>">
            </div>
        </div>
    </fieldset>

    <div class="row mb-3">
        <div class="col">
            <label for="id_agence_depart" class="form-label">Agence de départ</label>
            <select class="form-select" id="id_agence_depart" name="id_agence_depart" required>
                <option value="">-- Choisir --</option>
                <?php foreach ($agences as $agence): ?>
                    <option value="<?= (int) $agence['id_agence'] ?>"
                        <?= (($ancien['id_agence_depart'] ?? '') == $agence['id_agence']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($agence['nom_ville'], ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col">
            <label for="id_agence_arrivee" class="form-label">Agence d'arrivée</label>
            <select class="form-select" id="id_agence_arrivee" name="id_agence_arrivee" required>
                <option value="">-- Choisir --</option>
                <?php foreach ($agences as $agence): ?>
                    <option value="<?= (int) $agence['id_agence'] ?>"
                        <?= (($ancien['id_agence_arrivee'] ?? '') == $agence['id_agence']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($agence['nom_ville'], ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col">
            <label for="gdh_depart" class="form-label">Date et heure de départ</label>
            <input type="datetime-local" class="form-control" id="gdh_depart" name="gdh_depart" required
                   value="<?= htmlspecialchars($ancien['gdh_depart'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="col">
            <label for="gdh_arrivee" class="form-label">Date et heure d'arrivée</label>
            <input type="datetime-local" class="form-control" id="gdh_arrivee" name="gdh_arrivee" required
                   value="<?= htmlspecialchars($ancien['gdh_arrivee'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>
    </div>

    <div class="row mb-3">
        <div class="col">
            <label for="nb_places_total" class="form-label">Nombre total de places</label>
            <input type="number" min="1" class="form-control" id="nb_places_total" name="nb_places_total" required
                   value="<?= htmlspecialchars($ancien['nb_places_total'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="col">
            <label for="nb_places_disponibles" class="form-label">Places disponibles</label>
            <input type="number" min="0" class="form-control" id="nb_places_disponibles" name="nb_places_disponibles" required
                   value="<?= htmlspecialchars($ancien['nb_places_disponibles'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Créer le trajet</button>
</form>
