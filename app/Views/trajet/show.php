<h1 class="mb-4">Détail du trajet</h1>

<div class="card col-md-6">
    <div class="card-body">
        <p><strong>Départ :</strong> <?= htmlspecialchars($trajet['ville_depart'], ENT_QUOTES, 'UTF-8') ?>
            le <?= htmlspecialchars((new DateTime($trajet['gdh_depart']))->format('d/m/Y H:i'), ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>Arrivée :</strong> <?= htmlspecialchars($trajet['ville_arrivee'], ENT_QUOTES, 'UTF-8') ?>
            le <?= htmlspecialchars((new DateTime($trajet['gdh_arrivee']))->format('d/m/Y H:i'), ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>Places disponibles :</strong> <?= (int) $trajet['nb_places_disponibles'] ?></p>

        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalInfos">
            Voir les informations complémentaires
        </button>

        <?php if ($estAuteur): ?>
            <div class="mt-3 d-flex gap-2">
                <a href="/trajets/<?= (int) $trajet['id_trajet'] ?>/modifier" class="btn btn-secondary">Modifier</a>
                <form action="/trajets/<?= (int) $trajet['id_trajet'] ?>/supprimer" method="post"
                      onsubmit="return confirm('Confirmez-vous la suppression de ce trajet ?');">
                    <?= \App\Core\Csrf::field() ?>
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="modal fade" id="modalInfos" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Informations complémentaires</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <p><strong>Proposé par :</strong>
                    <?= htmlspecialchars($trajet['auteur_prenom'] . ' ' . $trajet['auteur_nom'], ENT_QUOTES, 'UTF-8') ?></p>
                <p><strong>Téléphone :</strong> <?= htmlspecialchars($trajet['auteur_telephone'], ENT_QUOTES, 'UTF-8') ?></p>
                <p><strong>Email :</strong> <?= htmlspecialchars($trajet['auteur_email'], ENT_QUOTES, 'UTF-8') ?></p>
                <p><strong>Nombre total de places :</strong> <?= (int) $trajet['nb_places_total'] ?></p>
            </div>
        </div>
    </div>
</div>
