// Menu Toggle
function toggleMenu() {
  const sideNav = document.querySelector('.side-nav');
  const overlay = document.querySelector('.nav-overlay');
  const menuBtn = document.querySelector('.menu-btn');
  
  sideNav.classList.toggle('show');
  overlay?.classList.toggle('active');
  menuBtn.classList.toggle('active');
  
  document.body.style.overflow = sideNav.classList.contains('show') ? 'hidden' : 'auto';
}

// Counter Animation
function animateCounters() {
  const counters = document.querySelectorAll('.stat-number');
  const speed = 200;

  counters.forEach(counter => {
      const updateCount = () => {
          const target = +counter.dataset.count;
          const count = +counter.innerText;
          
          const inc = target / speed;
          
          if(count < target) {
              counter.innerText = Math.ceil(count + inc);
              setTimeout(updateCount, 20);
          }
      }
      
      const observer = new IntersectionObserver((entries) => {
          entries.forEach(entry => {
              if(entry.isIntersecting) {
                  updateCount();
                  observer.unobserve(entry.target);
              }
          });
      });

      observer.observe(counter);
  });
}

// Initialize all functionality
document.addEventListener('DOMContentLoaded', () => {
  animateCounters();
  
  document.addEventListener('keydown', e => {
      if (e.key === 'Escape' && document.querySelector('.side-nav.show')) {
          toggleMenu();
      }
  });

  // Calendar Interaction
  document.querySelectorAll('.time-slots .btn').forEach(btn => {
      btn.addEventListener('click', function() {
          document.querySelectorAll('.time-slots .btn').forEach(b => b.classList.remove('active'));
          this.classList.add('active');
      });
  });

  // Date Selection
  document.querySelectorAll('.day:not(.disabled)').forEach(day => {
      day.addEventListener('click', function() {
          document.querySelectorAll('.day').forEach(d => d.classList.remove('active'));
          this.classList.add('active');
      });
  });

  // Image error handling
  document.querySelectorAll('img').forEach(img => {
      img.onerror = function() {
          this.src = 'https://via.placeholder.com/150';
          this.alt = 'Image non disponible';
      };
  });

  // Animation Observer
  const animateOnView = (selector) => {
      const elements = document.querySelectorAll(selector);
      const observer = new IntersectionObserver((entries, obs) => {
          entries.forEach(entry => {
              if(entry.isIntersecting) {
                  entry.target.classList.add('sweet-animate');
                  obs.unobserve(entry.target);
              }
          });
      }, { threshold: 0.5 });

      elements.forEach(el => observer.observe(el));
  };

  // Apply animations
  animateOnView('.hero, .featured-section, .stats-section, .why-us, .doctor-card, .step, .patient-dashboard, .history-item, .appointment-card');
});