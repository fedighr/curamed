document.addEventListener('DOMContentLoaded', function() {

    const forms = {
        account: document.getElementById('accountForm'),
        notifications: document.getElementById('notificationsForm'),
        security: document.getElementById('securityForm'),
        appearance: document.getElementById('appearanceForm')
    };


    for (const [formName, form] of Object.entries(forms)) {
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                saveSettings(formName);
            });
        }
    }

    const themeRadios = document.querySelectorAll('input[name="theme"]');
    themeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            applyTheme(this.value);
        });
    });

    const textSizeSlider = document.getElementById('textSize');
    if (textSizeSlider) {
        textSizeSlider.addEventListener('input', function() {
            updateTextSize(this.value);
        });
    }
});

function saveSettings(formType) {
    console.log(`Sauvegarde des paramètres ${formType}...`);

    

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

        const isDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        theme = isDark ? 'dark' : 'light';
    }
    
    document.body.classList.add(`${theme}-theme`);

}

function updateTextSize(size) {
    const sizes = ['small', 'medium', 'large'];
    document.body.classList.remove('text-small', 'text-medium', 'text-large');
    document.body.classList.add(`text-${sizes[size]}`);
}
document.addEventListener('DOMContentLoaded', () => {

    const profileDropdown = document.querySelector('.profile-dropdown');
    const dropdownMenu = document.querySelector('.dropdown-menu');

    profileDropdown.addEventListener('click', (e) => {
        e.stopPropagation();
        dropdownMenu.classList.toggle('show');
    });


    document.addEventListener('click', () => {
        dropdownMenu.classList.remove('show');
    });


    document.querySelector('.notifications-btn').addEventListener('click', () => {

        console.log('Afficher les notifications');
    });


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


document.addEventListener('click', (e) => {
    if (!notificationsWrapper.contains(e.target)) {
        notificationsDropdown.classList.remove('show');
    }
    if (!profileDropdown.contains(e.target)) {
        dropdownMenu.classList.remove('show');
    }
});

window.addEventListener('DOMContentLoaded', () => {
    const theme = localStorage.getItem('theme');
    if (theme === 'dark') {
      document.body.classList.add('dark-theme');
    }
  });
  

  document.getElementById("theme-toggle").addEventListener("click", () => {
    document.body.classList.toggle("dark-theme");
  

    const isDark = document.body.classList.contains("dark-theme");
    localStorage.setItem("theme", isDark ? "dark" : "light");
  });