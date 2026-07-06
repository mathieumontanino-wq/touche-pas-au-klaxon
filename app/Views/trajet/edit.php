<h1 class="mb-4">Modifier le trajet</h1>

<?php if (!empty($erreurs)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($erreurs as $erreur): ?>
                <li><?= htmlspecialchars($erreur, ENT_QUOTES, 'UTF-8') ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form action="/trajets/<?= (int) $trajet['id_trajet'] ?>/modifier" method="post" class="col-md-6">
    <?= $csrfField ?>

    <div class="row mb-3">
        <div class="col">
            <label for="id_agence_depart" class="form-label">Agence de départ</label>
            <select class="form-select" id="id_agence_depart" name="id_agence_depart" required>
                <?php foreach ($agences as $agence): ?>
                    <option value="<?= (int) $agence['id_agence'] ?>"
                        <?= ((int) $trajet['id_agence_depart'] === (int) $agence['id_agence']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($agence['nom_ville'], ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col">
            <label for="id_agence_arrivee" class="form-label">Agence d'arrivée</label>
            <select class="form-select" id="id_agence_arrivee" name="id_agence_arrivee" required>
                <?php foreach ($agences as $agence): ?>
                    <option value="<?= (int) $agence['id_agence'] ?>"
                        <?= ((int) $trajet['id_agence_arrivee'] === (int) $agence['id_agence']) ? 'selected' : '' ?>>
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
                   value="<?= htmlspecialchars((new DateTime($trajet['gdh_depart']))->format('Y-m-d\TH:i'), ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="col">
            <label for="gdh_arrivee" class="form-label">Date et heure d'arrivée</label>
            <input type="datetime-local" class="form-control" id="gdh_arrivee" name="gdh_arrivee" required
                   value="<?= htmlspecialchars((new DateTime($trajet['gdh_arrivee']))->format('Y-m-d\TH:i'), ENT_QUOTES, 'UTF-8') ?>">
        </div>
    </div>

    <div class="row mb-3">
        <div class="col">
            <label for="nb_places_total" class="form-label">Nombre total de places</label>
            <input type="number" min="1" class="form-control" id="nb_places_total" name="nb_places_total" required
                   value="<?= (int) $trajet['nb_places_total'] ?>">
        </div>
        <div class="col">
            <label for="nb_places_disponibles" class="form-label">Places disponibles</label>
            <input type="number" min="0" class="form-control" id="nb_places_disponibles" name="nb_places_disponibles" required
                   value="<?= (int) $trajet['nb_places_disponibles'] ?>">
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
</form>
