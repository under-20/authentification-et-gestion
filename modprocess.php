<?php
session_start();
$servername = "localhost"; 
$username = "isslem"; 
$password = "123123456isslem"; 
$dbname = "bookshop"; 

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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
        $stmt = $conn->prepare("SELECT id_user FROM users WHERE email = :email AND username != :username");
        $stmt->execute([':email' => $email, ':username' => $username]);
        
        if ($stmt->rowCount() > 0) {
            echo "Email déjà utilisé par un autre utilisateur.";
            exit;
        }

        // 2. Récupérer les infos de l'utilisateur courant
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();

        if (!$user) {
            echo "Utilisateur non trouvé.";
            exit;
        }

        // 3. Gestion de l'avatar
        $avatar = $user['avatar'];
        if (!empty($_FILES['avatar-upload']['name'])) {
            $targetDir = "uploads/avatars/";
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            $fileName = basename($_FILES["avatar-upload"]["name"]);
            $targetFile = $targetDir . time() . "_" . $fileName;
            $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

            if (in_array($fileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                if (move_uploaded_file($_FILES["avatar-upload"]["tmp_name"], $targetFile)) {
                    $avatar = $targetFile;
                }
            }
        }

        // 4. Gestion du mot de passe
        $updatePwd = false;
        if (!empty($newPwd) && !empty($currentPwd)) {
            if (!password_verify($currentPwd, $user['mdps'])) {
                echo "Mot de passe actuel incorrect.";
                exit;
            }

            if ($newPwd !== $confirmPwd) {
                echo "Les nouveaux mots de passe ne correspondent pas.";
                exit;
            }

            $hashedPwd = password_hash($newPwd, PASSWORD_DEFAULT);
            $updatePwd = true;
        }

        // 5. Mise à jour dans la base
        if ($updatePwd) {
            $stmt = $conn->prepare("UPDATE users SET email = :email, sexe = :sexe, role = :role, avatar = :avatar, mdps = :mdps WHERE username = :username");
            $stmt->execute([
                ':email' => $email,
                ':sexe' => $sexe,
                ':role' => $role,
                ':avatar' => $avatar,
                ':mdps' => $hashedPwd,
                ':username' => $username
            ]);
        } else {
            $stmt = $conn->prepare("UPDATE users SET email = :email, sexe = :sexe, role = :role, avatar = :avatar WHERE username = :username");
            $stmt->execute([
                ':email' => $email,
                ':sexe' => $sexe,
                ':role' => $role,
                ':avatar' => $avatar,
                ':username' => $username
            ]);
        }

        echo "Profil mis à jour avec succès.";
    }
} catch(PDOException $e) {
    echo "Erreur: " . $e->getMessage();
}
$conn = null;
?>