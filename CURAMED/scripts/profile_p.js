document.addEventListener('DOMContentLoaded', function() {
    const editBtn = document.getElementById('editBtn');
    const inputs = document.querySelectorAll('#profileForm input');
    let isEditing = false;
    

    editBtn.addEventListener('click', function() {
        isEditing = !isEditing;


        inputs.forEach(input => {
            input.disabled = !isEditing;
        });

        if (isEditing) {

            editBtn.innerHTML = '<i class="fas fa-save me-2"></i>Enregistrer';
            editBtn.classList.remove('btn-primary');
            editBtn.classList.add('btn-success');
        } else {

            const formData = new FormData(document.getElementById('profileForm'));
            
            inputs.forEach(input => input.disabled = false);
            saveChanges(formData);
            inputs.forEach(input => input.disabled = true);


            editBtn.innerHTML = '<i class="fas fa-edit me-2"></i>Modifier';
            editBtn.classList.remove('btn-success');
            editBtn.classList.add('btn-primary');
        }
    });

   

    function showAlert(message, type) {
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show`;
        alert.role = 'alert';
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        document.querySelector('.profile-header').after(alert);
        setTimeout(() => alert.remove(), 3000);
    }
    async function saveChanges() {
        const formData = new FormData(document.getElementById('profileForm'));
        
        try {
            const response = await fetch('update_profile.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(formData)
            });
            
            const result = await response.json();
            if(result.success) {
                showAlert(result.message || 'Modifications enregistrées!', 'success');
            } else {
                showAlert(result.message || 'Erreur lors de la mise à jour', 'danger');
            }
        } catch (error) {
            showAlert('Erreur réseau: ' + error.message, 'danger');
        }
    }
    
});