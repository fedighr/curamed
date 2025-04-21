document.addEventListener('DOMContentLoaded', function() {
    // Gestion des favoris
    document.querySelectorAll('.favorite-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const doctorId = this.dataset.doctorId;
            const isFavorite = this.classList.contains('active');
            
            // Ici, vous feriez une requête AJAX pour enregistrer le favori
            // Pour l'exemple, on simule juste le changement visuel
            this.classList.toggle('active');
            
            if (this.classList.contains('active')) {
                this.innerHTML = '<i class="fas fa-heart"></i> Favori';
            } else {
                this.innerHTML = '<i class="far fa-heart"></i> Ajouter aux favoris';
            }
        });
    });
    
    // Filtrage côté client (optionnel)
    const filterForm = document.querySelector('.filters form');
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            // Vous pourriez ajouter ici du filtrage côté client avant soumission
        });
    }
    
    
});
//Gestion du tri
    document.querySelectorAll('.sort-option').forEach(option => {
        option.addEventListener('click', function() {
            const sort = this.dataset.sort;
            const url = new URL(window.location.href);
            url.searchParams.set('sort', sort);
            window.location.href = url.toString();
        });
    });