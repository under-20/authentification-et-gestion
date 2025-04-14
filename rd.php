<?php
session_start();
require_once 'core/conn.php'; // Your PDO connection file

try {
    // Get database connection
    $db = Database::getConnection();

    // Delete user
    if (isset($_GET['delete_id'])) {
        $delete_id = $_GET['delete_id'];
        $delete_sql = "DELETE FROM users WHERE id_user = ?";
        $stmt = $db->prepare($delete_sql);
        $stmt->execute([$delete_id]);
        echo "Utilisateur supprimé avec succès.";
    }

    // Display users
    $sql = "SELECT id_user, username, email, mdps, sexe, role, avatar FROM users";
    $result = $db->query($sql);

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des utilisateurs</title>
    <link rel="stylesheet" href="rd.css">
</head>
<body>

<h2>Liste des utilisateurs</h2>

<?php
if ($result && $result->rowCount() > 0) {
    echo "<table>";
    echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Sexe</th><th>Rôle</th><th>Avatar</th><th>Actions</th></tr>";
    
    // Display data
    while($row = $result->fetch()) {
        echo "<tr>";
        echo "<td>" . $row["id_user"] . "</td>";
        echo "<td>" . $row["username"] . "</td>";
        echo "<td>" . $row["email"] . "</td>";
        echo "<td>" . $row["sexe"] . "</td>";
        echo "<td>" . $row["role"] . "</td>";
        echo "<td><img src='" . $row["avatar"] . "' alt='Avatar' width='50' height='50'></td>";
        echo "<td><a href='?delete_id=" . $row["id_user"] . "' class='btn-delete'>Supprimer</a></td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "Aucun utilisateur trouvé.";
}
?>

</body>
</html>