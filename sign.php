<?php



require_once __DIR__ . '/core/conn.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
    
        $db = Database::getConnection();

        $username = htmlspecialchars($_POST['username'] ?? '');
        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $password = $_POST['mdps'] ?? '';
        $sexe = htmlspecialchars($_POST['sexe'] ?? 'homme'); 

        // 5.controle de saisir
        if (empty($username)) {
            throw new Exception("Le nom d'utilisateur est requis");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Email invalide");
        }

        if (strlen($password) < 8) {
            throw new Exception("Le mot de passe doit contenir au moins 8 caractères");
        }

        // requête
        $stmt = $db->prepare("INSERT INTO users (username, email, mdps, sexe, role, avatar) 
                             VALUES (:username, :email, :password, :sexe, :role, :avatar)");

       
        $success = $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':password' => password_hash($password, PASSWORD_DEFAULT),
            ':sexe' => $sexe,
            ':role' => 'client',
            ':avatar' => 'default_avatar.png'
        ]);

      
        header("Location: authent.html?success=1");
        exit;

    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        header("Location: authent.html?error=db_error");
        exit;
    } catch (Exception $e) {
        header("Location: authent.html?error=" . urlencode($e->getMessage()));
        exit;
    }
} else {
   
    header("Location: authent.html");
    exit;
}