document.addEventListener('DOMContentLoaded', () => {
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, {
        threshold: 0.2
    });

    document.querySelectorAll('.animate').forEach(el => observer.observe(el));


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
    
    if (!track || !prevBtn || !nextBtn || cards.length === 0) {
        console.error("Éléments du carrousel introuvables");
        return;
    }

    let cardWidth = cards[0].offsetWidth + 16;
    let scrollPosition = 0;
    let autoScrollInterval;
    const scrollDuration = 3000;

    const scrollToPosition = (position) => {
        track.style.scrollBehavior = 'smooth';
        track.scrollLeft = position;
    };

    const scrollNext = () => {
        const maxScroll = track.scrollWidth - track.clientWidth;
        scrollPosition = Math.min(scrollPosition + cardWidth, maxScroll);
        scrollToPosition(scrollPosition);
        
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

 
        track.addEventListener('mouseenter', () => clearInterval(autoScrollInterval));
        track.addEventListener('mouseleave', startAutoScroll);
    };

    const startAutoScroll = () => {
        clearInterval(autoScrollInterval);
        autoScrollInterval = setInterval(scrollNext, scrollDuration);
    };

    const initCarousel = () => {
        cardWidth = cards[0].offsetWidth + 16;
        setupEventListeners();
        startAutoScroll();
    };

    const resizeObserver = new ResizeObserver(() => {
        cardWidth = cards[0].offsetWidth + 16;
    });
    resizeObserver.observe(track);

    initCarousel();


    window.addEventListener('beforeunload', () => {
        clearInterval(autoScrollInterval);
        resizeObserver.disconnect();
    });
});
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

document.querySelectorAll('.nav-icon').forEach(icon => {
    icon.addEventListener('mouseenter', () => {
        icon.style.transform = 'translateY(-3px)';
    });
    icon.addEventListener('mouseleave', () => {
        icon.style.transform = 'none';
    });
});

document.getElementById('search').addEventListener('submit', function(event) {
    const searchInput = document.querySelector('input[name="cherche"]').value.trim();
    const specialiteSelect = document.querySelector('select[name="specialite"]').value;

    if (searchInput === '' && specialiteSelect === '') {
        alert('Veuillez entrer un nom ou choisir une spécialité.');
        event.preventDefault();
    }
});