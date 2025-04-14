<?php
session_start();
require_once 'core/conn.php'; 

try {
    $db = Database::getConnection();

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['form-type']) && $_POST['form-type'] == 'create') {
        // Get mn bd
        $username = trim($_POST['new-username']);
        $email = trim($_POST['new-email']);
        $password = $_POST['new-pwd1'];
        $sexe = $_POST['new-sexe'];
        $role = $_POST['new-role'];
        $avatar = 'default_avatar.png';

        //  controle de saisir
        if (empty($username)) {
            throw new Exception("Username is required");
        }

        if (empty($email)) {
            throw new Exception("Email is required");
        }

        if (empty($password)) {
            throw new Exception("Password is required");
        }

        
        $stmt = $db->prepare("SELECT id_user FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        
        if ($stmt->rowCount() > 0) {
            throw new Exception("Username or email already exists");
        }

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert user
        $stmt = $db->prepare("INSERT INTO users (username, email, mdps, sexe, role, avatar) 
                            VALUES (?, ?, ?, ?, ?, ?)");
        
        $success = $stmt->execute([
            $username, 
            $email, 
            $hashed_password, 
            $sexe, 
            $role, 
            $avatar
        ]);

        if ($success) {
            $_SESSION['success'] = "User created successfully!";
        } else {
            throw new Exception("Error creating user");
        }
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Database error occurred";
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
}

header("Location: profile.html");
exit();
?>