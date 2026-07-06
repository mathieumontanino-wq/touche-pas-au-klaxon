<h1 class="mb-4">Trajets</h1>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Départ</th>
            <th>Date départ</th>
            <th>Arrivée</th>
            <th>Date arrivée</th>
            <th>Places dispo. / total</th>
            <th>Auteur</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($trajets as $trajet): ?>
            <tr>
                <td><?= htmlspecialchars($trajet['ville_depart'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars((new DateTime($trajet['gdh_depart']))->format('d/m/Y H:i'), ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($trajet['ville_arrivee'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars((new DateTime($trajet['gdh_arrivee']))->format('d/m/Y H:i'), ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= (int) $trajet['nb_places_disponibles'] ?> / <?= (int) $trajet['nb_places_total'] ?></td>
                <td><?= htmlspecialchars($trajet['auteur_prenom'] . ' ' . $trajet['auteur_nom'], ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                    <form action="/admin/trajets/<?= (int) $trajet['id_trajet'] ?>/supprimer" method="post"
                          onsubmit="return confirm('Confirmez-vous la suppression de ce trajet ?');">
                        <?= \App\Core\Csrf::field() ?>
                        <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
