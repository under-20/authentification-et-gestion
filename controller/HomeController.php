<?php
class HomeController {
    public function showHomePage() {
        // Démarrer la session si pas déjà fait
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Inclure la vue home.html
        require __DIR__.'/../view/frontoffice/home.html';
    }
}