<?php
session_start();
require_once __DIR__ . '/config/autoload.php';
require_once __DIR__ . '/config/config.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);




// Déterminer la page demandée
$page = $_GET['page'] ?? 'home';
$action = $_GET['action'] ?? null;

// Vérification du chemin avant inclusion
$authenPath = __DIR__ . '/view/frontoffice/authen.php';
if (!file_exists($authenPath)) {
    die("Erreur critique: Fichier authen.php introuvable. Chemin recherché: " . $authenPath);
}


switch ($page) {
    case 'auth':
        require_once __DIR__ . '/controller/AuthController.php';
        $authController = new AuthController($pdo);

        if ($action === 'logout') {
            $authController->handleLogout();
        } elseif ($action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $authController->handleLogin();
        } elseif ($action === 'register' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $authController->handleRegister();
        } elseif ($action === 'resetPassword' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $authController->sendPasswordResetEmail();
        } else {
            $authController->showAuthPage();
        }
        break;

    case 'home':
    default:
        require_once __DIR__ . '/controller/HomeController.php';
        $homeController = new HomeController();
        $homeController->showHomePage();
        break;
}
