<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
 
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord Admin</title>
</head>
<body>
    <h1>Bienvenue dans votre tableau de bord Admin</h1>
    <p>Bonjour, <?php echo $_SESSION['email']; ?> !</p>
    <a href="logout.php">Se déconnecter</a>
</body>
</html>
