<?php
class User
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // Créer un nouvel utilisateur
    public function createUser($data)
    {
        // Validation de l'email
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            error_log("Email invalide: " . $data['email']);
            return false;
        }

        // Vérification si l'utilisateur existe déjà
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $data['email']]);
        if ($stmt->rowCount() > 0) {
            error_log("L'email existe déjà dans la base de données : " . $data['email']);
            return false;
        }

        // Hash password
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        try {
            $stmt = $this->pdo->prepare("
           INSERT INTO users 
           (username, email, mdps, sexe, role, avatar) 
           VALUES (?, ?, ?, ?, 'client', 'default.png')
       ");

            return $stmt->execute([
                $data['username'],
                $data['email'],
                $hashedPassword,
                $data['sexe']
            ]);
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return false;
        }
    }



    // Trouver un utilisateur par son emailpublic function findUserByEmail($email) {
    public function findUserByEmail($email)
    {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    // Mettre à jour un utilisateur (par exemple, changer le mot de passe ou le profil)
    public function updateUser($userId, $data)
    {
        // Validation de l'email
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            error_log("Email invalide lors de la mise à jour : " . $data['email']);
            return false;
        }

        // Mise à jour de l'utilisateur dans la base de données
        $stmt = $this->pdo->prepare("UPDATE users SET email = :email, username = :username, sexe = :sexe 
                                     WHERE id = :id");
        $result = $stmt->execute([
            'email' => htmlspecialchars($data['email']),
            'username' => htmlspecialchars($data['username']),
            'sexe' => htmlspecialchars($data['sexe']),
            'id' => $userId
        ]);

        if ($result) {
            return true;
        } else {
            error_log("Erreur lors de la mise à jour de l'utilisateur avec ID : " . $userId);
            return false;
        }
    }

    // Supprimer un utilisateur
    public function deleteUser($userId)
    {
        // Supprimer l'utilisateur de la base de données
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = :id");
        $result = $stmt->execute(['id' => $userId]);

        if ($result) {
            return true;
        } else {
            error_log("Erreur lors de la suppression de l'utilisateur avec ID : " . $userId);
            return false;
        }
    }

    // Vérifier le mot de passe de l'utilisateur lors de la connexion
    public function verifyPassword($email, $password)
    {
        $user = $this->findUserByEmail($email);

        if ($user) {
            error_log("Mot de passe entré : $password");
            error_log("Hash stocké : " . $user['mdps']);
            if (password_verify($password, $user['mdps'])) {
                return $user;
            }
        }

        return false;
    }

    // Update user password
    public function updatePassword($userId, $newPassword)
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE users SET mdps = :password WHERE id_user = :id");
            return $stmt->execute([
                'password' => $newPassword, // Using plaintext as per existing implementation
                'id' => $userId
            ]);
        } catch (PDOException $e) {
            error_log("Database error during password update: " . $e->getMessage());
            return false;
        }
    }


}
?>