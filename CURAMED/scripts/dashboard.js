document.addEventListener('DOMContentLoaded', function() {

    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {

            document.querySelectorAll('.tab-btn, .content-section').forEach(el => {
                el.classList.remove('active');
            });
            

            this.classList.add('active');
            const targetSection = document.getElementById(this.dataset.target);
            targetSection.classList.add('active');
        });
    });


    const profileDropdown = document.getElementById('profileDropdown');
    const dropdownMenu = document.querySelector('.dropdown-menu');


    document.getElementById(`${sectionId}-section`).classList.add('active');
    event.currentTarget.classList.add('active');
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {

            document.querySelectorAll('.tab-btn, .content-section').forEach(el => {
                el.classList.remove('active');
            });
            

            this.classList.add('active');
            document.getElementById(this.dataset.target).classList.add('active');
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const profileDropdown = document.getElementById('profileDropdown');
    const dropdownMenu = document.querySelector('.dropdown-menu');

    profileDropdown.addEventListener('click', function(e) {
        e.stopPropagation();
        dropdownMenu.classList.toggle('show');
    });


    document.addEventListener('click', function(e) {
        if (!e.target.closest('#profileDropdown')) {
            dropdownMenu.classList.remove('show');
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {

    document.querySelectorAll('[data-target-modal="userModal"]').forEach(button => {
        button.addEventListener('click', () => {
            const modal = new bootstrap.Modal(document.getElementById('userModal'));
            const userType = button.dataset.userType;
            
            const specialiteField = document.querySelector('.specialite-field');
            if (userType === 'medecin') {
                specialiteField.style.display = 'block';
                specialiteField.querySelector('input').setAttribute('required', 'true');
            } else {
                specialiteField.style.display = 'none';
                specialiteField.querySelector('input').removeAttribute('required');
            }

            document.getElementById('modalUserType').value = userType;
            document.getElementById('userModalLabel').textContent = 
                userType === 'patient' ? 'Nouveau Patient' : 'Nouveau Médecin';
            
            document.querySelector('.specialite-field').style.display = 
                userType === 'medecin' ? 'block' : 'none';
            
            modal.show();
        });
    });
});

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-target-modal="editUserModal"]').forEach(button => {
        button.addEventListener('click', async () => {
            const modal = new bootstrap.Modal(document.getElementById('editUserModal'));
            const userId = button.getAttribute('data-user-id');
            const userType = button.getAttribute('data-user-type');

            try {
                const response = await fetch(`get_user_details.php?id=${userId}`);
                const userData = await response.json();

                document.getElementById('editUserId').value = userId;
                document.getElementById('editUserType').value = userType;
                document.getElementById('editNom').value = userData.nom || '';
                document.getElementById('editPrenom').value = userData.prenom || '';
                document.getElementById('editEmail').value = userData.email || '';
                document.getElementById('editTelephone').value = userData.telephone || '';

                const specialiteField = document.querySelector('.specialite-edit-field');
                if (userType === 'medecin') {
                    specialiteField.style.display = 'block';
                    document.getElementById('editSpecialite').value = userData.specialite || '';
                } else {
                    specialiteField.style.display = 'none';
                    document.getElementById('editSpecialite').value = '';
                }

                modal.show();
            } catch (err) {
                console.error('Erreur lors du chargement des données :', err);
                alert("Impossible de charger les données de l'utilisateur.");
            }
        });
    });
});