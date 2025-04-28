<?php
// Charger la connexion depuis config.php
require_once('../../config/config.php');

// Suppression d'un utilisateur si 'delete_id' est passé dans l'URL
if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);

    $delete_sql = "DELETE FROM users WHERE id_user = :id";
    $stmt = $pdo->prepare($delete_sql);
    $stmt->bindParam(':id', $delete_id, PDO::PARAM_INT);
    $stmt->execute();

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Récupération des utilisateurs avec recherche par nom et filtre par sexe
$search = '';
$sexe_filter = '';
$sort_by = 'username'; // tri par défaut

$where_clauses = [];
$params = [];

// Recherche par nom
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search = trim($_GET['search']);
    $where_clauses[] = "username LIKE :search";
    $params[':search'] = '%' . $search . '%';
}

// Filtre par sexe (homme/femme)
if (isset($_GET['sexe']) && in_array(strtoupper($_GET['sexe']), ['HOMME', 'H', 'FEMME', 'F'])) {
    $sexe_filter = strtoupper($_GET['sexe']);
    if ($sexe_filter === 'H' || $sexe_filter === 'HOMME') {
        $where_clauses[] = "(sexe = 'H' OR sexe = 'h' OR sexe = 'Homme')";
    } elseif ($sexe_filter === 'F' || $sexe_filter === 'FEMME') {
        $where_clauses[] = "(sexe = 'F' OR sexe = 'f' OR sexe = 'Femme')";
    }
}


// Tri (nom ou ID)
if (isset($_GET['sort_by']) && in_array($_GET['sort_by'], ['id_user', 'username'])) {
    $sort_by = $_GET['sort_by'];
}

// Construction de la requête SQL
$sql = "SELECT id_user, username, email, mdps, sexe, role, avatar FROM users";

if (!empty($where_clauses)) {
    $sql .= " WHERE " . implode(' AND ', $where_clauses);
}

$sql .= " ORDER BY $sort_by ASC";

$stmt = $pdo->prepare($sql);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();

$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Clients</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/rd.css">
</head>
<body>

<h2>Liste des Clients</h2>

<!-- Formulaire de recherche et filtre sexe -->
<form method="GET" action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="search-form">
    <input type="text" name="search" class="search-input" placeholder="Rechercher par nom..." value="<?= htmlspecialchars($search) ?>" oninput="this.form.submit();">
    
    <select name="sexe" class="search-select" onchange="this.form.submit();">
        <option value="">Tous</option>
        <option value="homme" <?= ($sexe_filter === 'Homme') ? 'selected' : '' ?>>Homme</option>
        <option value="femme" <?= ($sexe_filter === 'Femme') ? 'selected' : '' ?>>Femme</option>
    </select>
</form>

<br>

<?php if ($users): ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom d'utilisateur</th>
                <th>Email</th>
                <th>Mot de passe</th>
                <th>Sexe</th>
                <th>Rôle</th>
                <th>Avatar</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row["id_user"]) ?></td>
                    <td><?= htmlspecialchars($row["username"]) ?></td>
                    <td><?= htmlspecialchars($row["email"]) ?></td>
                    <td><?= htmlspecialchars($row["mdps"]) ?></td> <!-- attention mdps affiché en clair -->
                    <td><?= htmlspecialchars($row["sexe"]) ?></td>
                    <td><?= htmlspecialchars($row["role"]) ?></td>
                    <td>
                        <?php if (!empty($row["avatar"])): ?>
                            <img src="../../<?= htmlspecialchars($row["avatar"]) ?>" alt="Avatar" width="50" height="50">
                        <?php else: ?>
                            Aucun avatar
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="?delete_id=<?= htmlspecialchars($row["id_user"]) ?>" class="btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p style="text-align:center;">Aucun utilisateur trouvé.</p>
<?php endif; ?>

<!-- Tri par nom ou ID -->
<div class="sort-options">
    <form method="GET" action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="sort-form">
        <label>
            <input type="radio" name="sort_by" value="username" <?= ($sort_by === 'username') ? 'checked' : '' ?> onclick="this.form.submit();"> Nom
        </label>
        <label>
            <input type="radio" name="sort_by" value="id_user" <?= ($sort_by === 'id_user') ? 'checked' : '' ?> onclick="this.form.submit();"> ID
        </label>
    </form>
</div>

</body>
</html>
