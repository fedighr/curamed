document.addEventListener('DOMContentLoaded', function() {
    // Tab functionality
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active classes from all tab buttons and content sections
            document.querySelectorAll('.tab-btn, .content-section').forEach(el => {
                el.classList.remove('active');
            });
            // Add active class to the clicked button and its target section
            this.classList.add('active');
            const targetSection = document.getElementById(this.dataset.target);
            targetSection.classList.add('active');
        });
    });

    // Profile dropdown functionality
    const profileDropdown = document.getElementById('profileDropdown');
    const dropdownMenu = document.querySelector('.dropdown-menu');
    if (profileDropdown && dropdownMenu) {
        profileDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdownMenu.classList.toggle('show');
        });
    }
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#profileDropdown')) {
            dropdownMenu.classList.remove('show');
        }
    });

    // New user modal trigger for "Nouveau Patient" and "Nouveau Médecin"
    document.querySelectorAll('[data-target-modal="userModal"]').forEach(button => {
        button.addEventListener('click', () => {
            const modalElem = document.getElementById('userModal'); // Ensure this ID matches your modal container
            if (!modalElem) {
                console.error('Modal element with id "userModal" not found.');
                return;
            }
            const modal = new bootstrap.Modal(modalElem);
            const userType = button.dataset.userType; // 'patient' or 'medecin'

            // Reset the form
            document.getElementById('userForm').reset();
            document.getElementById('formAction').value = 'create';
            document.getElementById('userModalLabel').textContent =
                userType === 'patient' ? 'Nouveau Patient' : 'Nouveau Médecin';
            // Set the hidden input for user type so PHP receives the correct value
            document.getElementById('modalUserType').value = userType;

            // Specialité field handling (only for medecin)
            const specialiteField = document.querySelector('.specialite-field');
            if (userType === 'medecin') {
                specialiteField.style.display = 'block';
                specialiteField.querySelector('input').setAttribute('required', 'true');
            } else {
                specialiteField.style.display = 'none';
                specialiteField.querySelector('input').removeAttribute('required');
            }

            modal.show();
        });
    });

    // Edit user functionality (if applicable)
    document.querySelectorAll('.icon-btn[title="Modifier"]').forEach(btn => {
        btn.addEventListener('click', async function() {
            const modalElem = document.getElementById('editUserModal');
            if (!modalElem) {
                console.error('Edit modal not found.');
                return;
            }
            const modal = new bootstrap.Modal(modalElem);
            const userId = this.dataset.userId;
            const userType = this.dataset.userType;

            try {
                const response = await fetch(`get_user_details.php?id=${userId}`);
                if (!response.ok) throw new Error('Network response was not ok');
                const userData = await response.json();

                // Populate the edit form fields
                document.getElementById('editUserId').value = userId;
                document.getElementById('formAction').value = 'update';
                document.getElementById('modalUserType').value = userType;
                document.getElementById('editNom').value = userData.nom;
                document.getElementById('editPrenom').value = userData.prenom;
                document.getElementById('editEmail').value = userData.email;
                document.getElementById('editTelephone').value = userData.telephone;

                // Handle specialité field for medecin
                const specialiteField = document.querySelector('.specialite-field');
                if (userType === 'medecin') {
                    specialiteField.style.display = 'block';
                    document.getElementById('editSpecialite').value = userData.specialite || '';
                } else {
                    specialiteField.style.display = 'none';
                }

                // Handle current photo preview if available
                const photoPreview = document.getElementById('currentPhotoPreview');
                const currentPhotoSpan = document.getElementById('currentPhoto');
                if (userData.photo_profil) {
                    photoPreview.src = userData.photo_profil;
                    currentPhotoSpan.textContent = userData.photo_profil.split('/').pop();
                    photoPreview.style.display = 'block';
                } else {
                    photoPreview.style.display = 'none';
                    currentPhotoSpan.textContent = 'Aucune photo';
                }

                // Update modal title based on user type
                document.getElementById('userModalLabel').textContent =
                    userType === 'patient' ? 'Modifier Patient' : 'Modifier Médecin';

                modal.show();
            } catch (error) {
                console.error('Error:', error);
                alert('Erreur lors du chargement des données utilisateur');
            }
        });
    });

    // Password confirmation validation for the edit user form
    const editUserForm = document.getElementById('editUserForm');
    if (editUserForm) {
        editUserForm.addEventListener('submit', function(e) {
            const password = document.getElementById('editPassword').value;
            const confirmPassword = document.getElementById('editConfirmPassword').value;
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Les mots de passe ne correspondent pas!');
            }
        });
    }
});
