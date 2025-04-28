document.addEventListener('DOMContentLoaded', function() {
    const container = document.querySelector('.container');
    const registerBtn = document.querySelector('.register-btn');
    const loginBtn = document.querySelector('.login-btn');

    // Basculer entre login et register
    registerBtn.addEventListener('click', () => {
        container.classList.add('active');
        window.history.pushState({}, '', '?action=register');
    });

    loginBtn.addEventListener('click', () => {
        container.classList.remove('active');
        window.history.pushState({}, '', '?action=login');
    });

    // Gestion du paramètre d'URL au chargement
    const urlParams = new URLSearchParams(window.location.search);
    const action = urlParams.get('action');
    
    if (action === 'register') {
        container.classList.add('active');
    }
});

// Assure-toi que ce script est placé à la fin du fichier HTML ou après le chargement complet du DOM

function showResetForm() {
    // Affiche la section de réinitialisation du mot de passe
    document.querySelector('.reset-container').style.display = 'block';
}


