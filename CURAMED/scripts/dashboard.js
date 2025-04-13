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

document.addEventListener('DOMContentLoaded', function() {
    // Modal trigger logic
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
            
            // Toggle specialité field
            document.querySelector('.specialite-field').style.display = 
                userType === 'medecin' ? 'block' : 'none';
            
            modal.show();
        });
    });
});
