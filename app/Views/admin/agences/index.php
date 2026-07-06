<h1 class="mb-4">Agences</h1>

<a href="/admin/agences/creer" class="btn btn-primary mb-3">Créer une agence</a>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Ville</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($agences as $agence): ?>
            <tr>
                <td><?= htmlspecialchars($agence['nom_ville'], ENT_QUOTES, 'UTF-8') ?></td>
                <td class="d-flex gap-2">
                    <a href="/admin/agences/<?= (int) $agence['id_agence'] ?>/modifier" class="btn btn-sm btn-secondary">
                        Modifier
                    </a>
                    <form action="/admin/agences/<?= (int) $agence['id_agence'] ?>/supprimer" method="post"
                          onsubmit="return confirm('Confirmez-vous la suppression de cette agence ?');">
                        <?= \App\Core\Csrf::field() ?>
                        <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
