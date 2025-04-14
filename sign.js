const container =document.querySelector('.container');//select with class 1er to3redhk
const registerbtn =document.querySelector('.register-btn');
const loginbtn =document.querySelector('.login-btn');

// k teclick aal register buttonissm class ytzed maa active ywali container active
registerbtn.addEventListener('click',() =>{
    container.classList.add('active');
})

// tnahii active
loginbtn.addEventListener('click',()=>{
    container.classList.remove('active');
})


// Sélections de base
const forgetLink = document.querySelector('.forget-link a');
const resetContainer = document.querySelector('.reset-container');
const backToLogin = document.querySelector('.back-to-login a');

// Éléments du formulaire
const sendCodeBtn = document.getElementById('send-code-btn');
const verificationCode = document.querySelector('.verification-code');
const newPassword = document.querySelector('.new-password');
const verifyBtn = document.querySelector('.verify-btn');
const submitNewPassword = document.querySelector('.submit-new-password');

// 1. Ouverture de la fenêtre modale
forgetLink.addEventListener('click', (e) => {
    e.preventDefault();
    resetContainer.classList.add('active');
});

// 2. Fermeture de la fenêtre
backToLogin.addEventListener('click', (e) => {
    e.preventDefault();
    closeResetModal();
});

resetContainer.addEventListener('click', (e) => {
    if (e.target === resetContainer) {
        closeResetModal();
    }
});

// 3. Simulation d'envoi de code (UI seulement)
sendCodeBtn.addEventListener('click', () => {
    // Validation visuelle basique
    const email = document.getElementById('reset-email').value;
    if (!email.includes('@')) {
        alert('Format email invalide');
        return;
    }

    // Animation UI
    sendCodeBtn.style.display = 'none';
    verificationCode.style.display = 'block';
    verifyBtn.style.display = 'block';
    
    setTimeout(() => {
        verificationCode.style.opacity = '1';
        verifyBtn.style.opacity = '1';
    }, 10);
});

// 4. Simulation de vérification (UI seulement)
verifyBtn.addEventListener('click', () => {
    verificationCode.style.display = 'none';
    verifyBtn.style.display = 'none';
    newPassword.style.display = 'block';
    submitNewPassword.style.display = 'block';
    
    setTimeout(() => {
        newPassword.style.opacity = '1';
        submitNewPassword.style.opacity = '1';
    }, 10);
});

// 5. Bouton final (ne fait rien)
submitNewPassword.addEventListener('click', () => {
    alert('avec php');
    closeResetModal();
});

// Fonction de nettoyage
function closeResetModal() {
    resetContainer.classList.remove('active');
    resetForm();
}

function resetForm() {
    // Réinitialisation visuelle
    document.getElementById('reset-email').value = '';
    document.querySelector('.verification-code input').value = '';
    document.querySelector('.new-password input').value = '';
    
    sendCodeBtn.style.display = 'block';
    verificationCode.style.display = 'none';
    newPassword.style.display = 'none';
    verifyBtn.style.display = 'none';
    submitNewPassword.style.display = 'none';
    
    verificationCode.style.opacity = '0';
    verifyBtn.style.opacity = '0';
    newPassword.style.opacity = '0';
    submitNewPassword.style.opacity = '0';
}