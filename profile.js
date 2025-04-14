// Changement d'onglet
function switchTab(tabId) {
    document.querySelectorAll('.admin-form').forEach(form => {
        form.classList.remove('active-form');
    });

    document.getElementById(tabId).classList.add('active-form');

    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });

    event.currentTarget.classList.add('active');
}

//  avatar pour  formulaire
document.querySelectorAll('#avatar-upload').forEach(input => {
    input.addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (event) {
                const preview = input.closest('.avatar-preview').querySelector('img');
                preview.src = event.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
});

//  sombre / clair
document.addEventListener('DOMContentLoaded', function () {
    const darkModeToggle = document.createElement('button');
    darkModeToggle.id = 'dark-mode-toggle';
    darkModeToggle.innerHTML = '<i class="fas fa-moon"></i>';
    darkModeToggle.title = 'Basculer mode sombre/clair';

    const header = document.querySelector('h1');
    header.insertAdjacentElement('afterend', darkModeToggle);

    const savedMode = localStorage.getItem('darkMode');
    if (savedMode === 'enabled') {
        document.body.classList.add('dark-mode');
        darkModeToggle.innerHTML = '<i class="fas fa-sun"></i>';
    }

    darkModeToggle.addEventListener('click', function () {
        document.body.classList.toggle('dark-mode');

        if (document.body.classList.contains('dark-mode')) {
            localStorage.setItem('darkMode', 'enabled');
            this.innerHTML = '<i class="fas fa-sun"></i>';
        } else {
            localStorage.setItem('darkMode', 'disabled');
            this.innerHTML = '<i class="fas fa-moon"></i>';
        }
    });
});


function isValidEmail(email) {
    const regex = /^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/;
    return regex.test(email);
}

// cntrl  création 
document.getElementById('create-tab').addEventListener('submit', function (e) {
    const username = document.getElementById('new-username').value.trim();
    const email = document.getElementById('new-email').value.trim();
    const sexe = document.getElementById('new-sexe').value;
    const role = document.getElementById('new-role').value;
    const pwd1 = document.getElementById('new-pwd1').value;
    const pwd2 = document.getElementById('new-pwd2').value;

    if (username.length < 3) {
        alert("Le nom d'utilisateur doit contenir au moins 3 caractères.");
        e.preventDefault();
        return;
    }

    if (!isValidEmail(email)) {
        alert("L'adresse email n'est pas valide.");
        e.preventDefault();
        return;
    }

    if (sexe === "") {
        alert("Veuillez sélectionner le sexe.");
        e.preventDefault();
        return;
    }

    if (role === "") {
        alert("Veuillez sélectionner un rôle.");
        e.preventDefault();
        return;
    }

    if (pwd1.length < 6) {
        alert("Le mot de passe doit contenir au moins 6 caractères.");
        e.preventDefault();
        return;
    }

    if (pwd1 !== pwd2) {
        alert("Les mots de passe ne correspondent pas.");
        e.preventDefault();
        return;
    }
});

// Cntrl de modification 
document.getElementById('edit-tab').addEventListener('submit', function (e) {
    const username = document.getElementById('admin-username').value.trim();
    const email = document.getElementById('admin-email').value.trim();
    const newPwd = document.getElementById('new-pwd').value;
    const confirmPwd = document.getElementById('confirm-pwd').value;

    if (username.length < 3) {
        alert("Le nom d'utilisateur doit contenir au moins 3 caractères.");
        e.preventDefault();
        return;
    }

    if (!isValidEmail(email)) {
        alert("L'adresse email n'est pas valide.");
        e.preventDefault();
        return;
    }

    if (newPwd !== "" || confirmPwd !== "") {
        if (newPwd.length < 6) {
            alert("Le nouveau mot de passe doit contenir au moins 6 caractères.");
            e.preventDefault();
            return;
        }

        if (newPwd !== confirmPwd) {
            alert("Les nouveaux mots de passe ne correspondent pas.");
            e.preventDefault();
            return;
        }
    }
});
