 // Initialisation du style une seule fois
const initSidebarStyles = () => {
    if (!document.getElementById('sidebar-dynamic-styles')) {
        const style = document.createElement('style');
        style.id = 'sidebar-dynamic-styles';
        style.textContent = `
            .side.collapsed {
                width: 70px;
                transition: width 0.3s ease;
            }
            .side.collapsed .title,
            .side.collapsed > li:not(.logo) {
                display: none;
            }
            .side:not(.collapsed) {
                width: 250px;
                transition: width 0.3s ease;
            }
        `;
        document.head.appendChild(style);
    }
};

// Gestion du clic sur le bouton menu
document.querySelector('.bx-menu').addEventListener('click', function(e) {
    e.stopPropagation(); // Empêche la propagation
    initSidebarStyles();
    document.querySelector('.side').classList.toggle('collapsed');
});

// Empêche le toggle quand on clique dans la sidebar
document.querySelector('.side').addEventListener('click', function(e) {
    e.stopPropagation();
});

// Empêche le toggle quand on clique sur les liens
document.querySelectorAll('.side a').forEach(link => {
    link.addEventListener('click', function(e) {
        e.stopPropagation();
    });
});
 





/* -------------dombre et claire---------------------------------------- */

document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.getElementById('theme-toggle');
    const body = document.body;

    // Vérifier le thème au chargement
    function checkTheme() {
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark') {
            body.classList.add('dark');
            themeToggle.checked = true;
        }
    }

    // Appliquer le thème initial
    checkTheme();

    // Gestion du changement de thème
    themeToggle.addEventListener('change', function() {
        if (this.checked) {
            body.classList.add('dark');
            localStorage.setItem('theme', 'dark');
        } else {
            body.classList.remove('dark');
            localStorage.setItem('theme', 'light');
        }
    });


    /* ----------icons x et recherche */
  
    const searchInput = document.querySelector('.form-input input');
    const searchBtn = document.querySelector('.search-btn i');
    
    searchInput.addEventListener('focus', function() {
        searchBtn.classList.replace('bx-search-alt', 'bx-x');
        searchBtn.parentElement.type = 'button';
    });
    
    searchInput.addEventListener('blur', function() {
        if (searchInput.value === '') {
            searchBtn.classList.replace('bx-x', 'bx-search-alt');
            searchBtn.parentElement.type = 'submit';
        }
    });
    
    searchBtn.addEventListener('click', function() {
        if (searchBtn.classList.contains('bx-x')) {
            searchInput.value = '';
            searchInput.focus();
        }
    });
});



/* ************************ button move*************************************  */

// Sélection de tous les liens de la sidebar sauf celui avec la classe "logout"
const sideLinks = document.querySelectorAll('.side ul li a:not(.logout)');

// Ajout des écouteurs d'événements pour les liens
sideLinks.forEach(item => {
    const li = item.parentElement;

    item.addEventListener('click', () => {
        sideLinks.forEach(i => {
            i.parentElement.classList.remove('active');
        });
        li.classList.add('active');
    });
});




/* load ---------------------------------------------------------------- */
function loadContent(page) {
    document.getElementById('contentFrame').src = page;
}

  /* ---------------------------------------------------------------------------- */

/*profil down*/
document.addEventListener('DOMContentLoaded', function() {
    // Menu déroulant du profil
    const profileDropdown = document.querySelector('.profile-dropdown');
    const dropdownContent = profileDropdown.querySelector('.profile-dropdown-content');
    const dropdownLinks = dropdownContent.querySelectorAll('a');
    
    // Empêche la fermeture du menu quand on clique sur les liens
    dropdownLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.stopPropagation(); // Empêche la propagation du click
            // Ajoutez ici votre logique de navigation si nécessaire
            console.log('Option cliquée:', this.textContent);
            
            // Garde le menu ouvert
            dropdownContent.style.display = 'block';
        });
    });
    
    // Fermer le menu seulement quand on clique en dehors
    document.addEventListener('click', function(e) {
        if (!profileDropdown.contains(e.target)) {
            dropdownContent.style.display = 'none';
        }
    });
    
    // Ouvrir/fermeture au clic sur le profil
    profileDropdown.addEventListener('click', function(e) {
        e.stopPropagation();
        if (dropdownContent.style.display === 'block') {
            dropdownContent.style.display = 'none';
        } else {
            dropdownContent.style.display = 'block';
        }
    });
});


/* ------------------------- */






