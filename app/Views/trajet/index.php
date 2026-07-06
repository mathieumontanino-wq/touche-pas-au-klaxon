<h1 class="mb-4 text-center">Trajets disponibles</h1>

<?php if (empty($trajets)): ?>
    <p>Aucun trajet disponible pour le moment.</p>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>Agence de départ</th>
                    <th>Date de départ</th>
                    <th>Agence d'arrivée</th>
                    <th>Date d'arrivée</th>
                    <th>Places disponibles</th>
                    <?php if ($utilisateurConnecte !== null): ?>
                        <th>Actions</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($trajets as $trajet): ?>
                    <tr>
                        <td><?= htmlspecialchars($trajet['ville_depart'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((new DateTime($trajet['gdh_depart']))->format('d/m/Y H:i'), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($trajet['ville_arrivee'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((new DateTime($trajet['gdh_arrivee']))->format('d/m/Y H:i'), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= (int) $trajet['nb_places_disponibles'] ?></td>
                        <?php if ($utilisateurConnecte !== null): ?>
                            <td>
                                <a href="/trajets/<?= (int) $trajet['id_trajet'] ?>" class="btn btn-sm btn-primary">
                                    Détails
                                </a>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
