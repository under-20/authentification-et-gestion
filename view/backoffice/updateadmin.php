<?php 
session_start();
require_once('../../config/config.php'); // Inclure ton fichier de configuration

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['form-type'] === 'edit') {
    // Récupération des données du formulaire
    $username = trim($_POST['admin-username']);
    $email = trim($_POST['admin-email']);
    $sexe = $_POST['admin-sexe'];
    $role = $_POST['admin-role'];
    $currentPwd = $_POST['current-pwd'];
    $newPwd = $_POST['new-pwd'];
    $confirmPwd = $_POST['confirm-pwd'];

    // 1. Vérifier si l'email est déjà utilisé par un autre utilisateur
    $stmt = $pdo->prepare("SELECT id_user FROM users WHERE email = :email AND email != :currentEmail");
    $stmt->execute(['email' => $email, 'currentEmail' => $email]);

    if ($stmt->rowCount() > 0) {
        echo "Email déjà utilisé par un autre utilisateur.";
        exit;
    }

    // 2. Récupérer les informations de l'utilisateur courant via email
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if (!$user) {
        echo "Utilisateur non trouvé.";
        exit;
    }

    // 3. Gestion de l'avatar
    $avatar = $user['avatar']; // Conserver l'ancien avatar par défaut
    if (!empty($_FILES['avatar-upload']['name'])) {
        $targetDir = "../../assets/uploads/avatars/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileName = basename($_FILES["avatar-upload"]["name"]);
        $targetFile = $targetDir . time() . "_" . $fileName;
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        if (in_array($fileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            if (move_uploaded_file($_FILES["avatar-upload"]["tmp_name"], $targetFile)) {
                // On sauvegarde seulement le chemin relatif à partir de /assets/ pour être propre en BDD
                $avatar = "assets/uploads/avatars/" . time() . "_" . $fileName;
            }
        }
    }

    // 4. Gestion du mot de passe
    $updatePwd = false;
    if (!empty($newPwd) && !empty($currentPwd)) {
        if ($currentPwd !== $user['mdps']) {  // Comparaison sans hachage
            echo "Mot de passe actuel incorrect.";
            exit;
        }

        if ($newPwd !== $confirmPwd) {
            echo "Les nouveaux mots de passe ne correspondent pas.";
            exit;
        }

        // Utilisation du mot de passe tel quel sans hachage
        $updatePwd = true;
    }

    // 5. Mise à jour en base avec email comme critère
    if ($updatePwd) {
        $stmt = $pdo->prepare("UPDATE users 
            SET username = :username, sexe = :sexe, role = :role, avatar = :avatar, mdps = :mdps 
            WHERE email = :email");
        $success = $stmt->execute([
            'username' => $username,
            'sexe' => $sexe,
            'role' => $role,
            'avatar' => $avatar,
            'mdps' => $newPwd,  // Utilisation du mot de passe en clair
            'email' => $email
        ]);
    } else {
        $stmt = $pdo->prepare("UPDATE users 
            SET username = :username, sexe = :sexe, role = :role, avatar = :avatar 
            WHERE email = :email");
        $success = $stmt->execute([
            'username' => $username,
            'sexe' => $sexe,
            'role' => $role,
            'avatar' => $avatar,
            'email' => $email
        ]);
    }

    if ($success) {
        echo "Profil mis à jour avec succès.";
    } else {
        echo "Erreur lors de la mise à jour.";
    }
}
?>


<!-- Ton HTML en dessous -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Profil Admin</title>
    <link rel="stylesheet" href="../../assets/css/profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="admin-profile-container">
        <h1><i class="fas fa-user-cog"></i> Modifier le profil administrateur</h1>

        <div class="form-container">
            <form id="edit-form" class="admin-form active-form" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="form-type" value="edit">

                <div class="avatar-section">
                    <div class="avatar-preview">
                        <img id="avatar-img" src="default-avatar.jpg" alt="Avatar">
                        <label for="avatar-upload" class="upload-btn">
                            <i class="fas fa-camera"></i> 
                        </label>
                        <input type="file" id="avatar-upload" name="avatar-upload" accept="image/*">
                    </div>
                </div>

                <div class="form-group">
                    <label for="admin-username"><i class="fas fa-at"></i> Nom d'utilisateur</label>
                    <input type="text" id="admin-username" name="admin-username" placeholder="Nom d'utilisateur" required>
                </div>

                <div class="form-group">
                    <label for="admin-email"><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" id="admin-email" name="admin-email" placeholder="admin@example.com" required>
                </div>

                <div class="form-group">
                    <label for="admin-sexe"><i class="fas fa-venus-mars"></i> Sexe</label>
                    <select id="admin-sexe" name="admin-sexe">
                        <option value="homme">Homme</option>
                        <option value="femme">Femme</option>
                        <option value="autre">Autre</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="admin-role"><i class="fas fa-user-tag"></i> Rôle</label>
                    <select id="admin-role" name="admin-role">
                        <option value="superadmin">Super Admin</option>
                        <option value="admin" selected>Admin</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="current-pwd"><i class="fas fa-lock"></i> Mot de passe actuel</label>
                    <input type="password" id="current-pwd" name="current-pwd">
                </div>

                <div class="form-group">
                    <label for="new-pwd"><i class="fas fa-key"></i> Nouveau mot de passe</label>
                    <input type="password" id="new-pwd" name="new-pwd">
                </div>

                <div class="form-group">
                    <label for="confirm-pwd"><i class="fas fa-key"></i> Confirmer mot de passe</label>
                    <input type="password" id="confirm-pwd" name="confirm-pwd">
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn save-btn">Enregistrer</button>
                    <button type="reset" class="btn cancel-btn">Annuler</button>
                </div>

            </form>
        </div>
    </div>

    <script src="../../assets/js/profile.js"></script>
</body>
</html>
