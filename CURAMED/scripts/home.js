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

    let cardWidth = document.querySelector(".doctor-card").offsetWidth + 16;
    let scrollPosition = 0;

    // Défilement manuel avec les boutons
    nextBtn.addEventListener("click", () => {
      scrollNext();
    });

    prevBtn.addEventListener("click", () => {
      scrollPrev();
    });

    function scrollNext() {
      const maxScroll = track.scrollWidth - track.clientWidth;
      if (scrollPosition + cardWidth < maxScroll) {
        scrollPosition += cardWidth;
      } else {
        scrollPosition = 0; // Retour au début
      }
      track.scrollTo({ left: scrollPosition, behavior: "smooth" });
    }

    function scrollPrev() {
      if (scrollPosition - cardWidth > 0) {
        scrollPosition -= cardWidth;
      } else {
        scrollPosition = 0;
      }
      track.scrollTo({ left: scrollPosition, behavior: "smooth" });
    }

    // Autoplay toutes les 3 secondes
    const autoScroll = setInterval(() => {
      scrollNext();
    }, 3000);

    // Recalcule la largeur d’une carte si la fenêtre change
    window.addEventListener("resize", () => {
      cardWidth = document.querySelector(".doctor-card").offsetWidth + 16;
    });
  });