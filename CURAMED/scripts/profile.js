document.addEventListener('DOMContentLoaded', function() {
    // Variables globales
    let currentDate = new Date();
    let selectedDate = null;
    let selectedTime = null;
    const calendarDays = document.getElementById('calendar-days');
    const timeSlotsContainer = document.getElementById('time-slots');
    const currentMonthElement = document.getElementById('current-month');
    const selectedDateInput = document.getElementById('selected-date');
    const selectedTimeInput = document.getElementById('selected-time');
    const dateHeureInput = document.getElementById('date-heure');
    const rdvForm = document.getElementById('rdv-form');

    // Initialisation de la carte Google Maps
    function initMap() {
        const map = new google.maps.Map(document.getElementById('map'), {
            zoom: 15,
            center: {lat: 48.8566, lng: 2.3522} // Paris par défaut
        });
        
        // Vous pouvez ajouter un marqueur avec l'adresse du médecin
        new google.maps.Marker({
            position: {lat: 48.8566, lng: 2.3522},
            map: map,
            title: "Cabinet du Dr."
        });
    }

    // Générer le calendrier
    function generateCalendar(date) {
        const year = date.getFullYear();
        const month = date.getMonth();
        
        // Mettre à jour le mois affiché
        currentMonthElement.textContent = date.toLocaleDateString('fr-FR', { 
            month: 'long', 
            year: 'numeric' 
        }).replace(/^\w/, c => c.toUpperCase());

        // Premier et dernier jour du mois
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        
        // Jours du mois précédent
        const prevMonthLastDay = new Date(year, month, 0).getDate();
        const firstWeekDay = firstDay.getDay() === 0 ? 6 : firstDay.getDay() - 1;
        
        let daysHtml = '';
        
        // Entêtes des jours
        const dayNames = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
        dayNames.forEach(day => {
            daysHtml += `<div class="day-header">${day}</div>`;
        });

        // Jours du mois précédent
        for(let i = firstWeekDay; i > 0; i--) {
            daysHtml += `<div class="day disabled">${prevMonthLastDay - i + 1}</div>`;
        }

        // Jours du mois courant
        for(let i = 1; i <= lastDay.getDate(); i++) {
            const dayDate = new Date(year, month, i);
            const isPast = dayDate < new Date().setHours(0, 0, 0, 0);
            
            daysHtml += `
                <div class="day 
                    ${isPast ? 'disabled' : ''}
                    ${i === selectedDate?.getDate() && month === selectedDate?.getMonth() ? 'selected' : ''}"
                    data-date="${dayDate.toISOString()}">
                    ${i}
                </div>
            `;
        }

        calendarDays.innerHTML = daysHtml;
    }

    // Générer les créneaux horaires
    function generateTimeSlots(date) {
        if (!date) return;
        
        const slots = [];
        const startHour = 9;
        const endHour = 18;
        const interval = 30; // minutes
        
        for(let hour = startHour; hour < endHour; hour++) {
            for(let minute = 0; minute < 60; minute += interval) {
                const time = `${hour.toString().padStart(2, '0')}:${minute.toString().padStart(2, '0')}`;
                const slotDate = new Date(date);
                slotDate.setHours(hour, minute);
                
                // Vérifier si le créneau est disponible (non pris)
                const isAvailable = !isTimeSlotTaken(slotDate);
                
                if(isAvailable) {
                    slots.push(`
                        <button type="button" class="time-slot" 
                            data-time="${time}"
                            data-datetime="${slotDate.toISOString()}">
                            ${time}
                        </button>
                    `);
                }
            }
        }
        
        timeSlotsContainer.innerHTML = slots.join('');
    }

    // Vérifier si un créneau est déjà pris
    function isTimeSlotTaken(dateTime) {
        // Dans une vraie application, vous feriez une requête AJAX pour vérifier
        // Pour cet exemple, on simule avec des créneaux aléatoires pris
        return Math.random() > 0.7; // 30% de chance que le créneau soit pris
    }

    // Gestion des événements
    document.querySelector('.prev-month').addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        generateCalendar(currentDate);
    });

    document.querySelector('.next-month').addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        generateCalendar(currentDate);
    });

    calendarDays.addEventListener('click', (e) => {
        const day = e.target.closest('.day:not(.disabled)');
        if(!day) return;

        selectedDate = new Date(day.dataset.date);
        selectedDateInput.value = selectedDate.toLocaleDateString('fr-FR');
        generateTimeSlots(selectedDate);
        
        // Réinitialiser l'heure sélectionnée
        selectedTime = null;
        selectedTimeInput.value = '';
        dateHeureInput.value = '';
        
        document.querySelectorAll('.day').forEach(d => d.classList.remove('selected'));
        day.classList.add('selected');
    });

    timeSlotsContainer.addEventListener('click', (e) => {
        const slot = e.target.closest('.time-slot');
        if(!slot) return;

        selectedTime = slot.dataset.time;
        selectedTimeInput.value = selectedTime;
        dateHeureInput.value = slot.dataset.datetime;
        
        document.querySelectorAll('.time-slot').forEach(s => s.classList.remove('selected'));
        slot.classList.add('selected');
    });

    // Validation du formulaire
    rdvForm.addEventListener('submit', (e) => {
        if(!selectedDate || !selectedTime) {
            e.preventDefault();
            alert('Veuillez sélectionner une date et un horaire');
        }
    });

    // Initialisation
    generateCalendar(currentDate);
    window.initMap = initMap;
});