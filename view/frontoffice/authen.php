<?php
require_once __DIR__ . '/../../config/config.php';

$error = $_SESSION['error'] ?? null;
$success = $_SESSION['success'] ?? null;
unset($_SESSION['error'], $_SESSION['success']);
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentification</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css'
        rel='stylesheet'>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/authen.css">
</head>

<body>
    <style>
        .reset-container {
            display: none;
            /* Caché par défaut */
            position: fixed;
            /* Position fixe pour qu'il reste visible même en scrollant */
            top: 0;
            /* Place en haut de la page */
            left: 0;
            width: 100%;
            /* Prend toute la largeur de la page */
            height: 100%;
            /* Prend toute la hauteur de la page */
            background-color: rgba(0, 0, 0, 0.7);
            /* Fond semi-transparent */
            display: none;
            /* Reste caché jusqu'à ce qu'on l'affiche via JS */
            justify-content: center;
            /* Centre le contenu verticalement */
            align-items: center;
            /* Centre le contenu horizontalement */
            z-index: 9999;
            /* S'assure que le formulaire est au-dessus des autres éléments */
        }
    </style>
    <div
        class="container <?= isset($_GET['action']) && $_GET['action'] === 'register' ? 'active' : '' ?>">

        <!-- -------------------------------------------------------------------------------------------------- -->
        <!-- Formulaire de Login -->


        <div class="form-box login">

            <form method="POST" action="?page=auth&action=login">


                <h1 class="btn-shine">login</h1>
                <?php if ($error): ?>
                    <div class="error-message"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="success-message"><?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>



                <div class="input-box">
                    <input type="text" placeholder="email" name="email"
                        required>
                    <i class='bx bxs-user-circle'></i>
                </div>

                <div class="input-box">
                    <input type="password" placeholder="password"
                        name="password" required>
                    <i class='bx bxs-lock-alt' id="lock"></i>
                </div>

                <div class="forget-link">
                    <a href="#" onclick="showResetForm()">forget password</a>

                </div>

                <button type="submit" name="login" class="btn">login</button>

                <p>login with social platforms</p>
                <div class="social-icons">
                    <a href="#"><i
                            class='facebook bx bxl-facebook-circle'></i></a>
                    <a href="#"><i class='bx bxl-google'></i></a>
                    <a href="#"><i class='bx bxl-github'></i></a>
                </div>
            </form>
        </div>




        <!-- -------------------------------------------------------------------------------------------------------------------------------------------- -->

        <!-- Formulaire registre -->
        <div class="form-box register">
            <form method="POST" action="?page=auth&action=register">
                <h1 class="btn-shine">registration</h1>

                <div class="input-box">
                    <input type="text" placeholder="username" name="username"
                        required>
                    <i class='bx bxs-user-circle'></i>
                </div>

                <div class="input-box">
                    <input type="email" placeholder="email" name="email"
                        required>
                    <i class='bx bxs-envelope'></i>
                </div>

                <div class="input-box">
                    <input type="password" placeholder="password"
                        name="password" required>
                    <i class='bx bxs-lock-alt' id="lock"></i>
                </div>

                <div class="input">
                    <div class="checkbox-container">
                        <input class="checkbox-input" id="animated-checkbox"
                            type="radio" name="sexe" value="femme" />
                        <label class="checkbox" for="animated-checkbox">
                            <span class="line line1"></span>
                            <span class="line line2"></span>
                        </label>
                        <label for="animated-checkbox">femme</label>
                    </div>
                    <br>
                    <div class="checkbox-container1">
                        <input class="checkbox-input1" id="animated-checkbox1"
                            type="radio" name="sexe" value="homme" />
                        <label class="checkbox1" for="animated-checkbox1">
                            <span class="linee line3"></span>
                            <span class="linee line4"></span>
                        </label>
                        <label for="animated-checkbox1">homme</label>
                    </div>
                </div>

                <button type="submit" name="register"
                    class="btn">register</button>

                <p>or register with social platforms</p>
                <div class="social-icons">
                    <a href="#"><i
                            class='bx bxl-facebook-circle facebook'></i></a>
                    <a href="#"><i class='bx bxl-google'></i></a>
                    <a href="#"><i class='bx bxl-github'></i></a>
                </div>
            </form>
        </div>

        <!-- Zone de bascule -->
        <div class="toggle-box">
            <div class="toggle-panel toggle-left">
                <h1>hello, welcome!</h1>
                <p>Don't have an account?</p>
                <button class="btn register-btn">Register</button>
            </div>

            <div class="toggle-panel toggle-right">
                <h1>welcome back!</h1>
                <p>Already have an account?</p>
                <button class="btn login-btn">login</button>
            </div>
        </div>
    </div>

    <div class="reset-container" style="display: none;">
        <div class="form-box reset-password" method="POST">
            <form id="reset-password-form">
                <h1 class="btn-shine">Reset Password</h1>
                <div class="input-box">
                    <input type="email" placeholder="Enter your email"
                        id="reset-email" required>
                    <i class='bx bxs-envelope'></i>
                </div>
                <button type="button" class="btn"
                    id="send-code-btn">Generate</button>
                <div id="qrcode" style="margin: 20px auto; width: 200px;"></div>
                <div id="message" style="text-align: center; margin-top: 10px;">
                </div>
                <div class="back-to-login">
                    <a href="#" onclick="showResetForm()"><i
                            class='bx bx-arrow-back'></i> Back to login</a>
                </div>
            </form>
        </div>
    </div>

    <script src="<?= BASE_URL ?>/assets/js/authen.js"></script>

</body>

</html>



<!-- ----------------------------------------------------------------------- -->
<script>
    // Fonction unique pour afficher/masquer le formulaire
    function showResetForm() {
        const resetContainer = document.querySelector('.reset-container');
        resetContainer.style.display = resetContainer.style.display === 'block' ? 'none' : 'block';
        // Réinitialiser le contenu quand on montre le formulaire
        if (resetContainer.style.display === 'block') {
            document.getElementById('reset-email').value = '';
            document.getElementById('qrcode').innerHTML = '';
            document.getElementById('message').innerHTML = '';
        }
    }

    // Add event listener to the Generate button for password reset
    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('send-code-btn').addEventListener('click', function () {
            const email = document.getElementById('reset-email').value;

            // Validate email
            if (!email || !email.includes('@')) {
                document.getElementById('message').innerHTML = '<span style="color: red;">Please enter a valid email address</span>';
                return;
            }

            // Show loading message
            document.getElementById('message').innerHTML = '<span style="color: blue;">Generating password reset and creating QR code...</span>';

            // Send AJAX request
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '?page=auth&action=resetPassword', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            xhr.onreadystatechange = function () {
                if (this.readyState === XMLHttpRequest.DONE) {
                    console.log("Response received:", this.responseText); // Log the raw response

                    if (this.status === 200) {
                        try {
                            const response = JSON.parse(this.responseText);
                            if (response.status === 'success') {
                                document.getElementById('message').innerHTML = '<span style="color: green;">' + response.message + '</span>';

                                // If there's a QR code URL, display it in the qrcode div
                                if (response.qrCodeUrl) {
                                    document.getElementById('qrcode').innerHTML = '';
                                }
                            } else {
                                document.getElementById('message').innerHTML = '<span style="color: red;">' + response.message + '</span>';
                                if (response.debug_info) {
                                    console.error("Debug info:", response.debug_info);
                                }
                            }
                        } catch (e) {
                            // If we can't parse the JSON, display the raw response or error
                            console.error("Error parsing JSON:", e);
                            document.getElementById('message').innerHTML = '<span style="color: red;">Error processing response. See console for details.</span>';
                            console.log("Raw response:", this.responseText);
                        }
                    } else {
                        // HTTP error status
                        document.getElementById('message').innerHTML = '<span style="color: red;">HTTP error: ' + this.status + '</span>';
                    }
                }
            };

            // Add error handler for network errors
            xhr.onerror = function () {
                document.getElementById('message').innerHTML = '<span style="color: red;">Network error. Check console for details.</span>';
                console.error("Network error occurred");
            };

            xhr.send('email=' + encodeURIComponent(email));
        });
    });
</script>