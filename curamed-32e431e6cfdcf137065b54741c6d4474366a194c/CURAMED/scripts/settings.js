document.addEventListener('DOMContentLoaded', function() {
    // Gestion des formulaires
    const forms = {
        account: document.getElementById('accountForm'),
        notifications: document.getElementById('notificationsForm'),
        security: document.getElementById('securityForm'),
        appearance: document.getElementById('appearanceForm')
    };

    // Soumission des formulaires
    for (const [formName, form] of Object.entries(forms)) {
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                saveSettings(formName);
            });
        }
    }

    // Gestion du changement de thème
    const themeRadios = document.querySelectorAll('input[name="theme"]');
    themeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            applyTheme(this.value);
        });
    });

    // Gestion de la taille du texte
    const textSizeSlider = document.getElementById('textSize');
    if (textSizeSlider) {
        textSizeSlider.addEventListener('input', function() {
            updateTextSize(this.value);
        });
    }
});

function saveSettings(formType) {
    console.log(`Sauvegarde des paramètres ${formType}...`);
    // Ici vous ajouteriez la logique AJAX pour sauvegarder
    
    // Feedback visuel
    const button = document.querySelector(`#${formType}Form button[type="submit"]`);
    const originalText = button.innerHTML;
    
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Enregistrement...';
    
    setTimeout(() => {
        button.innerHTML = '<i class="fas fa-check me-2"></i>Enregistré!';
        setTimeout(() => {
            button.innerHTML = originalText;
            button.disabled = false;
        }, 1500);
    }, 1000);
}

function applyTheme(theme) {
    console.log(`Application du thème ${theme}`);
    document.body.classList.remove('light-theme', 'dark-theme');
    
    if (theme === 'system') {
        // Détection du thème système
        const isDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        theme = isDark ? 'dark' : 'light';
    }
    
    document.body.classList.add(`${theme}-theme`);
    // Ici vous pourriez sauvegarder le thème dans les cookies/localStorage
}

function updateTextSize(size) {
    const sizes = ['small', 'medium', 'large'];
    document.body.classList.remove('text-small', 'text-medium', 'text-large');
    document.body.classList.add(`text-${sizes[size]}`);
}
document.addEventListener('DOMContentLoaded', () => {
    // Gestion du dropdown profil
    const profileDropdown = document.querySelector('.profile-dropdown');
    const dropdownMenu = document.querySelector('.dropdown-menu');

    profileDropdown.addEventListener('click', (e) => {
        e.stopPropagation();
        dropdownMenu.classList.toggle('show');
    });

    // Fermer le dropdown quand on clique ailleurs
    document.addEventListener('click', () => {
        dropdownMenu.classList.remove('show');
    });

    // Notifications
    document.querySelector('.notifications-btn').addEventListener('click', () => {
        // Ajouter la logique d'affichage des notifications ici
        console.log('Afficher les notifications');
    });

    // Animation au survol du logo
    const logo = document.querySelector('.logo-img');
    logo.addEventListener('mouseenter', () => {
        logo.style.transform = 'scale(1.05)';
    });
    logo.addEventListener('mouseleave', () => {
        logo.style.transform = 'scale(1)';
    });
});

const notificationsWrapper = document.querySelector('.notifications-wrapper');
const notificationsDropdown = document.querySelector('.notifications-dropdown');

notificationsWrapper.addEventListener('click', (e) => {
    e.stopPropagation();
    notificationsDropdown.classList.toggle('show');
});

// Fermer les dropdowns au clic externe
document.addEventListener('click', (e) => {
    if (!notificationsWrapper.contains(e.target)) {
        notificationsDropdown.classList.remove('show');
    }
    if (!profileDropdown.contains(e.target)) {
        dropdownMenu.classList.remove('show');
    }
});
