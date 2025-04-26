document.addEventListener('DOMContentLoaded', function() {
    // Gestion du calendrier
    let currentDate = new Date();
    
    function updateCalendar() {
        const calendar = document.getElementById('calendar');
        const startDate = new Date(currentDate);
        startDate.setDate(startDate.getDate() - startDate.getDay() + 1); // Début semaine (lundi)
        
        let calendarHTML = '';
        
        for(let i = 0; i < 5; i++) {
            const day = new Date(startDate);
            day.setDate(day.getDate() + i);
            
            calendarHTML += `
                <div class="calendar-day">
                    <div class="calendar-day-header">
                        ${day.toLocaleDateString('fr-FR', { weekday: 'short', day: 'numeric' })}
                    </div>
                    <div class="appointments">
                        <!-- Rendez-vous simulés -->
                        ${i === 1 ? `
                            <div class="appointment-item">
                                <div class="appointment-time">09:00 - 09:30</div>
                                <div class="appointment-patient">Marie Dupont</div>
                                <div class="appointment-type">Consultation</div>
                            </div>
                        ` : ''}
                    </div>
                </div>`;
        }
        
        calendar.innerHTML = calendarHTML;
        document.getElementById('currentWeek').textContent = 
            `Semaine ${getWeekNumber(currentDate)} - ${currentDate.getFullYear()}`;
    }

    document.getElementById('prevWeek').addEventListener('click', function() {
        currentDate.setDate(currentDate.getDate() - 7);
        updateCalendar();
    });

    document.getElementById('nextWeek').addEventListener('click', function() {
        currentDate.setDate(currentDate.getDate() + 7);
        updateCalendar();
    });

    // Gestion des patients
    document.querySelectorAll('.patient-item').forEach(item => {
        item.addEventListener('click', function() {
            // Ouvrir le dossier patient
            console.log('Ouverture dossier patient');
        });
    });

    // Initialisation
    updateCalendar();
});

function getWeekNumber(date) {
    const d = new Date(date);
    d.setHours(0,0,0);
    d.setDate(d.getDate() + 4 - (d.getDay()||7));
    const yearStart = new Date(d.getFullYear(),0,1);
    return Math.ceil(((d - yearStart) / 86400000 + 1)/7);
}

document.addEventListener('DOMContentLoaded', function() {
    const saveBtn = document.querySelector('.save-btn');
    
    saveBtn.addEventListener('click', function(e) {
        e.preventDefault();
        const errors = validateSchedule();
        
        if(errors.length === 0) {
            this.closest('form').submit();
        } else {
            showErrors(errors);
        }
    });

    function validateSchedule() {
        const errors = [];
        const rows = document.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const isOff = row.querySelector('.off-checkbox').checked;
            const inputs = row.querySelectorAll('.time-select');
            const times = Array.from(inputs).map(select => select.value);
            
            if(!isOff) {
                // Check all time fields are filled
                if(times.some(t => t === "")) {
                    errors.push(`Tous les champs horaires doivent être remplis pour ${row.cells[0].textContent}`);
                    return;
                }

                // Convert times to minutes
                const [start, pauseStart, pauseEnd, end] = times.map(t => {
                    const [h, m] = t.split(':').map(Number);
                    return h * 60 + m;
                });

                // Validate time order
                if(start >= pauseStart) errors.push(`Début doit être avant Pause début (${row.cells[0].textContent})`);
                if(pauseStart >= pauseEnd) errors.push(`Pause début doit être avant Pause fin (${row.cells[0].textContent})`);
                if(pauseEnd >= end) errors.push(`Pause fin doit être avant Fin (${row.cells[0].textContent})`);
            }
        });

        return errors;
    }

    function showErrors(errors) {
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger';
        alertDiv.innerHTML = `
            <h5>Erreurs de validation:</h5>
            <ul>${errors.map(e => `<li>${e}</li>`).join('')}</ul>
        `;
        
        const form = document.querySelector('form');
        form.prepend(alertDiv);
        
        setTimeout(() => alertDiv.remove(), 5000);
    }
});


function scrollToFirstError() {
    const firstError = document.querySelector('.is-invalid');
    if(firstError) {
        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}

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