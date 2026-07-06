<h1 class="mb-4">Utilisateurs</h1>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Email</th>
            <th>Téléphone</th>
            <th>Rôle</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($utilisateurs as $utilisateur): ?>
            <tr>
                <td><?= htmlspecialchars($utilisateur['nom'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($utilisateur['prenom'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($utilisateur['email'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($utilisateur['telephone'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($utilisateur['role'], ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
