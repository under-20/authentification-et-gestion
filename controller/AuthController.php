<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../model/user.php';
class AuthController
{
    private $userModel;
    private $avatarDir = __DIR__ . '/../../public/assets/uploads/avatars/';

    public function __construct($pdo)
    {
        $this->userModel = new User($pdo);
    }

    /* ////////////////////////////////////////////////////////////////////// */

    public function handleLogin()
    {
        error_log("handleLogin() appelée");

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            error_log("Méthode POST détectée");

            // Récupération des données du formulaire
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';

            error_log("Email reçu : $email");
            error_log("Password reçu : " . ($password ? 'OK' : 'VIDE'));

            // Validation email
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = "Veuillez entrer un email valide";
                error_log("Erreur : Email invalide");
                echo "Erreur : Email invalide";
                exit();
            }

            // Validation mot de passe
            if (empty($password)) {
                $_SESSION['error'] = "Veuillez entrer un mot de passe";
                error_log("Erreur : Mot de passe vide");
                echo "Erreur : Mot de passe vide";
                exit();
            }

            // Recherche utilisateur
            $user = $this->userModel->findUserByEmail($email);
            error_log("Utilisateur trouvé : " . print_r($user, true));

            if (!$user) {
                $_SESSION['error'] = "L'email n'existe pas";
                error_log("Erreur : utilisateur introuvable");
                echo "Erreur : utilisateur introuvable";
                exit();
            }

            // Vérification du mot de passe en texte clair
            if ($password !== $user['mdps']) {  // Comparaison directe en texte clair
                $_SESSION['error'] = "Mot de passe incorrect";
                error_log("Erreur : mot de passe incorrect");
                echo "Erreur : mot de passe incorrect";
                exit();
            }

            // Création de session utilisateur
            $_SESSION['user'] = [
                'id_user' => $user['id_user'],
                'username' => $user['username'],
                'email' => $user['email'],
                'sexe' => $user['sexe'],
                'role' => $user['role'],
                'avatar' => $user['avatar'] ?? 'default.png'
            ];

            error_log("SESSION USER CRÉÉE : " . print_r($_SESSION['user'], true));

            // Test final de redirection
            if ($user['email'] === 'aya@esprit.tn') {
                /*  error_log("Redirection : Admin");
                 echo "Redirection vers Admin"; */
                header("Location: view/backoffice/admindash.html");
                exit();
            } else {

                header("Location: view/frontoffice/home.html");
                exit();
            }
        } else {
            error_log("handleLogin() appelée sans POST");
            echo "Erreur : méthode non autorisée";
            exit();
        }
    }





















    /* ////////////////////////////////////////////////////////////////////// */
    public function handleRegister()
    {
        error_log("Registration attempt: " . print_r($_POST, true));

        // Validate input
        if (empty($_POST['username']) || empty($_POST['email']) || empty($_POST['password'])) {
            $_SESSION['error'] = "All fields are required";
            header("Location: ?action=register");
            exit();
        }




        // Process registration
        $data = [
            'username' => trim($_POST['username']),
            'email' => filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL),
            'password' => $_POST['password'],
            'sexe' => $_POST['sexe'] ?? 'h'
        ];

        if ($this->userModel->createUser($data)) {
            $_SESSION['success'] = "Registration successful! Please login.";
            header("Location: ?action=login");
        } else {
            $_SESSION['error'] = "Registration failed. Email may already exist.";
            header("Location: ?action=register");
        }
        exit();
    }



    /* ////////////logout//////////////////////// */




    public function handleLogout()
    {
        // Destruction de session et redirection
        session_destroy();
        header("Location: ?page=home");
    }




    /* ////////////////////////////////////////////////////////////////////// */
    public function showAuthPage()
    {
        $error = $_SESSION['error'] ?? null;
        $success = $_SESSION['success'] ?? null;
        unset($_SESSION['error'], $_SESSION['success']);

        require __DIR__ . '/../view/frontoffice/authen.php';
    }




    /* ////////////////////////////////////////////////////////////////////// */
    public function sendPasswordResetEmail()
    {
        // Get the email from the POST request
        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);

        try {
            // Check if email exists in database
            $user = $this->userModel->findUserByEmail($email);

            if (!$user) {
                echo json_encode(['status' => 'error', 'message' => 'Email not found']);
                exit();
            }

            // Generate a simple random password
            $newPassword = substr(md5(rand()), 0, 8);

            // Update user password in database
            $result = $this->userModel->updatePassword($user['id_user'], $newPassword);
            if (!$result) {
                throw new Exception("Failed to update password in database");
            }

            // Using a different QR code API (QR Server)
            $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" . urlencode($newPassword);

            // Send email with QR code and password
            $to = $email;
            $subject = 'Password Reset';

            // Email body with HTML to display the QR code AND the plain text password
            $message = "
            <html>
            <head>
                <title>Password Reset</title>
            </head>
            <body>
                <h1>Password Reset</h1>
                <p>Hello " . htmlspecialchars($user['username']) . ",</p>
                <p>Your password has been reset.</p>
                
                <h2>Your new password is: <strong>" . $newPassword . "</strong></h2>
                
                <p>You can also scan this QR code to get your password:</p>
                <p><img src='{$qrCodeUrl}' alt='QR Code containing your new password' style='max-width:250px;'></p>
                
                <p>Please login with this password and change it as soon as possible.</p>
                <p>Best regards,<br>The Team</p>
            </body>
            </html>";

            // Set headers for HTML email
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=UTF-8\r\n";
            $headers .= "From: noreply@yourwebsite.com\r\n";

            $mailSent = mail($to, $subject, $message, $headers);

            // Return success with the QR code URL for displaying in the UI
            echo json_encode([
                'status' => 'success',
                'message' => 'Password reset successful! QR code generated and email sent.',
                'qrCodeUrl' => $qrCodeUrl,
                'password' => $newPassword // Include password for debugging
            ]);

            exit();

        } catch (Exception $e) {
            // Log the error for server logs
            error_log("Password reset error: " . $e->getMessage());

            // Return specific error to frontend
            echo json_encode([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
            exit();
        }
    }

}
?>