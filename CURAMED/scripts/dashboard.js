document.addEventListener('DOMContentLoaded', function() {
    // Tab functionality
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active states
            document.querySelectorAll('.tab-btn, .content-section').forEach(el => {
                el.classList.remove('active');
            });
            
            // Add active states
            this.classList.add('active');
            const targetSection = document.getElementById(this.dataset.target);
            targetSection.classList.add('active');
        });
    });

    // Profile dropdown (existing code remains the same)
    const profileDropdown = document.getElementById('profileDropdown');
    const dropdownMenu = document.querySelector('.dropdown-menu');
    // ... rest of profile dropdown code

    document.getElementById(`${sectionId}-section`).classList.add('active');
    event.currentTarget.classList.add('active');
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active states
            document.querySelectorAll('.tab-btn, .content-section').forEach(el => {
                el.classList.remove('active');
            });
            
            // Add active states
            this.classList.add('active');
            document.getElementById(this.dataset.target).classList.add('active');
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
    // Use the ID we added to target the container
    const profileDropdown = document.getElementById('profileDropdown');
    const dropdownMenu = document.querySelector('.dropdown-menu');

    // Toggle dropdown when clicking anywhere in the profile container
    profileDropdown.addEventListener('click', function(e) {
        e.stopPropagation();
        dropdownMenu.classList.toggle('show');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#profileDropdown')) {
            dropdownMenu.classList.remove('show');
        }
    });
});

document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        if(confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) {
            const userId = this.dataset.userId;
            const userType = this.dataset.userType;
            
            fetch('dashboard.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `delete_user=1&user_id=${userId}&user_type=${userType}`
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    this.closest('tr').remove();
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-success';
                    alert.textContent = data.type === 'patient' ? 
                        'Patient supprimé avec succès' : 
                        'Médecin supprimé avec succès';
                    document.body.prepend(alert);
                    setTimeout(() => alert.remove(), 3000);
                } else {
                    alert('Erreur lors de la suppression');
                }
            })
                    }
                });
            });

