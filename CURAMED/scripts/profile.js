// doctor-profile.js
document.addEventListener('DOMContentLoaded', () => {
    // Gestion du calendrier
    const calendarDays = document.querySelector('.days-grid');
    const monthHeader = document.querySelector('.month-header h3');
    const prevMonthBtn = document.querySelector('.prev-month');
    const nextMonthBtn = document.querySelector('.next-month');
    
    // Gestion des créneaux horaires
    const timeSlotsContainer = document.querySelector('.slots-grid');
    const appointmentForm = document.querySelector('.appointment-form');
    const selectedDateInput = document.querySelector('.selected-date');
    const selectedTimeInput = document.querySelector('.selected-time');
    
    let currentDate = new Date();
    let selectedDate = null;
    let selectedTime = null;

    // Générer le calendrier
    function generateCalendar(date) {
        const year = date.getFullYear();
        const month = date.getMonth();
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const monthName = date.toLocaleString('fr-FR', { month: 'long', year: 'numeric' });
        
        monthHeader.textContent = monthName.charAt(0).toUpperCase() + monthName.slice(1);
        
        let daysHtml = '';
        
        // Jours du mois précédent
        const prevMonthLastDay = new Date(year, month, 0).getDate();
        const firstWeekDay = firstDay.getDay() === 0 ? 6 : firstDay.getDay() - 1;
        
        for(let i = firstWeekDay; i > 0; i--) {
            daysHtml += `<div class="day disabled">${prevMonthLastDay - i + 1}</div>`;
        }

        // Jours du mois courant
        for(let i = 1; i <= lastDay.getDate(); i++) {
            const dayDate = new Date(year, month, i);
            const isAvailable = Math.random() > 0.5; // Simulation de disponibilité
            const isPast = dayDate < new Date().setHours(0,0,0,0);
            
            daysHtml += `
                <div class="day 
                    ${isPast ? 'disabled' : ''} 
                    ${isAvailable && !isPast ? 'available' : ''}
                    ${i === selectedDate?.getDate() ? 'selected' : ''}"
                    data-date="${dayDate.toISOString()}">
                    ${i}
                </div>
            `;
        }

        calendarDays.innerHTML = daysHtml;
    }

    // Générer les créneaux horaires
    function generateTimeSlots(date) {
        const slots = [];
        const startHour = 9;
        const endHour = 18;
        const interval = 30; // minutes
        
        for(let hour = startHour; hour < endHour; hour++) {
            for(let minute = 0; minute < 60; minute += interval) {
                const time = `${hour.toString().padStart(2, '0')}:${minute.toString().padStart(2, '0')}`;
                const isAvailable = Math.random() > 0.5; // Simulation de disponibilité
                
                if(isAvailable) {
                    slots.push(`
                        <button type="button" class="time-slot" 
                            data-time="${time}">
                            ${time}
                        </button>
                    `);
                }
            }
        }
        
        timeSlotsContainer.innerHTML = slots.join('');
    }

    // Gestion des clics sur les jours
    calendarDays.addEventListener('click', (e) => {
        const day = e.target.closest('.day:not(.disabled)');
        if(!day) return;

        selectedDate = new Date(day.dataset.date);
        selectedDateInput.value = selectedDate.toLocaleDateString('fr-FR');
        generateTimeSlots(selectedDate);
        
        document.querySelectorAll('.day').forEach(d => d.classList.remove('selected'));
        day.classList.add('selected');
    });

    // Gestion des clics sur les créneaux
    timeSlotsContainer.addEventListener('click', (e) => {
        const slot = e.target.closest('.time-slot');
        if(!slot) return;

        selectedTime = slot.dataset.time;
        selectedTimeInput.value = selectedTime;
        
        document.querySelectorAll('.time-slot').forEach(s => s.classList.remove('selected'));
        slot.classList.add('selected');
    });

    // Navigation calendrier
    prevMonthBtn.addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        generateCalendar(currentDate);
    });

    nextMonthBtn.addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        generateCalendar(currentDate);
    });

    // Soumission du formulaire
    appointmentForm.addEventListener('submit', (e) => {
        e.preventDefault();
        
        if(!selectedDate || !selectedTime) {
            showAlert('Veuillez sélectionner une date et un horaire', 'error');
            return;
        }

        const appointmentDate = new Date(selectedDate);
        const [hours, minutes] = selectedTime.split(':');
        appointmentDate.setHours(hours, minutes);

        // Simulation d'envoi au backend
        setTimeout(() => {
            showAlert('Rendez-vous confirmé !', 'success');
            resetForm();
        }, 1000);
    });

    // Fonctions utilitaires
    function showAlert(message, type = 'success') {
        const alert = document.createElement('div');
        alert.className = `alert alert-${type}`;
        alert.textContent = message;
        
        document.body.appendChild(alert);
        
        setTimeout(() => {
            alert.remove();
        }, 3000);
    }

    function resetForm() {
        selectedDate = null;
        selectedTime = null;
        selectedDateInput.value = '';
        selectedTimeInput.value = '';
        document.querySelectorAll('.selected').forEach(el => el.classList.remove('selected'));
    }

    // Initialisation
    generateCalendar(currentDate);
    generateTimeSlots(new Date());
});