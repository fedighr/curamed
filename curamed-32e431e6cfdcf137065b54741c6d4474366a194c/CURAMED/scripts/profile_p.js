document.addEventListener('DOMContentLoaded', function() {
    // Gestion de la photo de profil
    const avatarInput = document.getElementById('avatarInput');
    const profileAvatar = document.querySelector('.profile-avatar');
    
    if(avatarInput && profileAvatar) {
        avatarInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    profileAvatar.src = e.target.result;
                    // Ici vous pouvez ajouter un appel AJAX pour sauvegarder
                    console.log('Photo de profil mise à jour');
                }
                reader.readAsDataURL(file);
            }
        });
    }

    // Gestion du mode édition
    const editBtn = document.getElementById('editBtn');
    if(editBtn) {
        editBtn.addEventListener('click', toggleEdit);
    }

    // Gestion des contacts d'urgence
    document.querySelectorAll('.emergency-contact .edit-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            const contactItem = e.target.closest('.emergency-contact');
            toggleContactEdit(contactItem, true);
        });
    });

    // Gestion de l'annulation de l'édition
    document.addEventListener('click', function(e) {
        if(e.target.classList.contains('cancel-btn')) {
            const contactItem = e.target.closest('.emergency-contact');
            toggleContactEdit(contactItem, false);
        }
    });

    // Fonctionnalité d'ajout de contact
    const addContactBtn = document.getElementById('addContactBtn');
    if(addContactBtn) {
        addContactBtn.addEventListener('click', showContactForm);
    }
});

function toggleEdit() {
    const form = document.getElementById('profileForm');
    const inputs = form.querySelectorAll('input');
    const isDisabled = inputs[0].disabled;
    
    inputs.forEach(input => input.disabled = !isDisabled);
    this.classList.toggle('active');
    this.innerHTML = isDisabled ? 
        '<i class="fas fa-save me-2"></i>Sauvegarder' : 
        '<i class="fas fa-edit me-2"></i>Modifier';

    if(!isDisabled) {
        console.log('Sauvegarde des modifications...');
        // Ajouter ici la logique de sauvegarde AJAX
    }
}

function toggleContactEdit(contactItem, showEdit) {
    const isEditing = contactItem.classList.contains('editing');
    if(typeof showEdit === 'boolean') {
        if(showEdit) contactItem.classList.add('editing');
        else contactItem.classList.remove('editing');
    } else {
        contactItem.classList.toggle('editing');
    }
}

function showContactForm() {
    const formTemplate = `
        <div class="emergency-contact editing mb-3">
            <form class="contact-form">
                <div class="row g-2">
                    <div class="col-md-6">
                        <input type="text" class="form-control" placeholder="Nom" required>
                    </div>
                    <div class="col-md-6">
                        <input type="text" class="form-control" placeholder="Relation" required>
                    </div>
                    <div class="col-12">
                        <input type="tel" class="form-control" placeholder="Téléphone" required>
                    </div>
                    <div class="col-12 text-end">
                        <button type="button" class="btn btn-secondary cancel-btn">Annuler</button>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </div>
            </form>
        </div>`;
    
    const contactsList = document.querySelector('.emergency-contacts');
    contactsList.insertAdjacentHTML('afterbegin', formTemplate);

    // Gestion de la soumission du formulaire
    const form = contactsList.querySelector('.contact-form');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        // Ici vous ajouteriez la logique de sauvegarde
        console.log('Nouveau contact enregistré');
        this.closest('.emergency-contact').classList.remove('editing');
    });
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
