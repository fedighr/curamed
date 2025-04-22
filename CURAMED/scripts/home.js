document.addEventListener('DOMContentLoaded', () => {
    // Ajouter la classe visible dès qu'un élément entre dans la vue
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, {
        threshold: 0.2
    });

    // Sélection des éléments à animer
    document.querySelectorAll('.animate').forEach(el => observer.observe(el));

    // Recherche (logique simple pour test)
    document.querySelector('.search-button').addEventListener('click', () => {
        const query = document.querySelector('.search-input').value;
        console.log('Recherche:', query);
    });
});
document.addEventListener("DOMContentLoaded", () => {
    const track = document.querySelector(".carousel-track");
    const prevBtn = document.querySelector(".prev-btn");
    const nextBtn = document.querySelector(".next-btn");
    const cards = document.querySelectorAll(".doctor1-card");
    
    // Variables avec gestion des erreurs
    if (!track || !prevBtn || !nextBtn || cards.length === 0) {
        console.error("Éléments du carrousel introuvables");
        return;
    }

    let cardWidth = cards[0].offsetWidth + 16;
    let scrollPosition = 0;
    let autoScrollInterval;
    const scrollDuration = 3000; // 3 secondes

    // Fonctions améliorées
    const scrollToPosition = (position) => {
        track.style.scrollBehavior = 'smooth';
        track.scrollLeft = position;
    };

    const scrollNext = () => {
        const maxScroll = track.scrollWidth - track.clientWidth;
        scrollPosition = Math.min(scrollPosition + cardWidth, maxScroll);
        scrollToPosition(scrollPosition);
        
        // Réinitialiser si fin atteinte
        if (scrollPosition >= maxScroll) {
            setTimeout(() => {
                scrollPosition = 0;
                track.style.scrollBehavior = 'auto';
                track.scrollLeft = 0;
            }, 3000);
        }
    };

    const scrollPrev = () => {
        scrollPosition = Math.max(scrollPosition - cardWidth, 0);
        scrollToPosition(scrollPosition);
    };

    // Gestion des événements
    const setupEventListeners = () => {
        nextBtn.addEventListener("click", () => {
            clearInterval(autoScrollInterval);
            scrollNext();
            startAutoScroll();
        });

        prevBtn.addEventListener("click", () => {
            clearInterval(autoScrollInterval);
            scrollPrev();
            startAutoScroll();
        });

        // Pause au survol
        track.addEventListener('mouseenter', () => clearInterval(autoScrollInterval));
        track.addEventListener('mouseleave', startAutoScroll);
    };

    const startAutoScroll = () => {
        clearInterval(autoScrollInterval); // Nettoyer avant de redémarrer
        autoScrollInterval = setInterval(scrollNext, scrollDuration);
    };

    // Initialisation
    const initCarousel = () => {
        cardWidth = cards[0].offsetWidth + 16;
        setupEventListeners();
        startAutoScroll();
    };

    // Redimensionnement optimisé
    const resizeObserver = new ResizeObserver(() => {
        cardWidth = cards[0].offsetWidth + 16;
    });
    resizeObserver.observe(track);

    // Démarrer le carrousel
    initCarousel();

    // Nettoyage
    window.addEventListener('beforeunload', () => {
        clearInterval(autoScrollInterval);
        resizeObserver.disconnect();
    });
});
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

// Animation hover
document.querySelectorAll('.nav-icon').forEach(icon => {
    icon.addEventListener('mouseenter', () => {
        icon.style.transform = 'translateY(-3px)';
    });
    icon.addEventListener('mouseleave', () => {
        icon.style.transform = 'none';
    });
});