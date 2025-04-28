document.addEventListener('DOMContentLoaded', function() {

    let currentDate = new Date();
    let selectedDate = null;
    let selectedTime = null;
    const calendarDays = document.getElementById('calendar-days5');
    const timeSlotsContainer = document.getElementById('time-slots15');
    const currentMonthElement = document.getElementById('calendar-current-month5');
    const selectedDateInput = document.getElementById('selected-date5');
    const selectedTimeInput = document.getElementById('selected-time5');
    const dateHeureInput = document.getElementById('date-heure5');
    const rdvForm = document.getElementById('rdv-form5');
    const prevMonthBtn = document.querySelector('.calendar-nav5.prev5');
    const nextMonthBtn = document.querySelector('.calendar-nav5.next5');


    function generateCalendar(date) {
        const year = date.getFullYear();
        const month = date.getMonth();
        const today = new Date();
        
        currentMonthElement.textContent = date.toLocaleDateString('fr-FR', { 
            month: 'long', 
            year: 'numeric' 
        }).replace(/^\w/, c => c.toUpperCase());

        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const prevMonthLastDay = new Date(year, month, 0).getDate();
        const firstWeekDay = firstDay.getDay() === 0 ? 6 : firstDay.getDay() - 1;
        
        let daysHtml = '';
        

        const dayNames = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
        dayNames.forEach(day => {
            daysHtml += `<div class="calendar-day-header5">${day}</div>`;
        });


        for(let i = firstWeekDay; i > 0; i--) {
            daysHtml += `<div class="calendar-day5 disabled">${prevMonthLastDay - i + 1}</div>`;
        }


        for(let i = 1; i <= lastDay.getDate(); i++) {
            const dayDate = new Date(year, month, i);
            const isPast = dayDate < new Date(today.getFullYear(), today.getMonth(), today.getDate());
            const isToday = dayDate.getDate() === today.getDate() && 
                          month === today.getMonth() && 
                          year === today.getFullYear();
            const isSelected = selectedDate && 
                              i === selectedDate.getDate() && 
                              month === selectedDate.getMonth() && 
                              year === selectedDate.getFullYear();
            
            daysHtml += `
                <div class="calendar-day5 
                    ${isPast ? 'disabled' : ''}
                    ${isSelected ? 'selected' : ''}
                    ${isToday ? 'today' : ''}"
                    data-date="${dayDate.toISOString()}">
                    ${i}
                </div>
            `;
        }


        const daysShown = firstWeekDay + lastDay.getDate();
        const remainingDays = 7 - (daysShown % 7);
        if (remainingDays < 7) {
            for(let i = 1; i <= remainingDays; i++) {
                daysHtml += `<div class="calendar-day5 disabled">${i}</div>`;
            }
        }

        calendarDays.innerHTML = daysHtml;
        

        if (selectedDate) {
            const selectedDayElement = document.querySelector(`.calendar-day5[data-date="${selectedDate.toISOString()}"]`);
            if (selectedDayElement) {
                selectedDayElement.classList.add('selected');
            }
        }
    }


    async function generateTimeSlots(date) {
        if (!date) {
            timeSlotsContainer.innerHTML = '<p class="text-center">Sélectionnez une date pour voir les disponibilités</p>';
            return;
        }
        

        timeSlotsContainer.innerHTML = `
            <div class="loading-slots">
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
            </div>
        `;
        

        await new Promise(resolve => setTimeout(resolve, 800));
        
        const slots = [];
        const startHour = 8;
        const endHour = 20;
        const interval = 30;
        
        for(let hour = startHour; hour < endHour; hour++) {
            for(let minute = 0; minute < 60; minute += interval) {
                const time = `${hour.toString().padStart(2, '0')}:${minute.toString().padStart(2, '0')}`;
                const slotDate = new Date(date);
                slotDate.setHours(hour, minute);
                

                const isBooked = await checkSlotAvailability(slotDate);
                const isPast = slotDate < new Date();
                
                if(!isBooked && !isPast) {
                    const isSelected = selectedTime === time;
                    slots.push(`
                        <button type="button" class="time-slot5 ${isSelected ? 'selected' : ''}" 
                            data-time="${time}"
                            data-datetime="${slotDate.toISOString()}">
                            ${time}
                        </button>
                    `);
                } else if (isBooked) {
                    slots.push(`
                        <button type="button" class="time-slot5 booked" disabled
                            data-time="${time}"
                            data-datetime="${slotDate.toISOString()}">
                            ${time}
                        </button>
                    `);
                }
            }
        }
        
        if (slots.length === 0) {
            timeSlotsContainer.innerHTML = `
                <p class="text-center">Aucun créneau disponible pour cette date</p>
                <button type="button" class="btn-refresh5" onclick="refreshSlots()">
                    <i class="fas fa-sync-alt"></i> Actualiser
                </button>
            `;
        } else {
            timeSlotsContainer.innerHTML = slots.join('');
        }
    }

    // Vérifier la disponibilité d'un créneau (simulation)
    async function checkSlotAvailability(dateTime) {
        // Dans une vraie application, vous feriez une requête AJAX ici
        // Simulation : 20% de chance que le créneau soit pris
        return new Promise(resolve => {
            setTimeout(() => {
                resolve(Math.random() < 0.2);
            }, 50);
        });
    }

    // Actualiser les créneaux
    window.refreshSlots = function() {
        if (selectedDate) {
            generateTimeSlots(selectedDate);
        }
    };

    // Navigation dans le calendrier
    prevMonthBtn.addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        generateCalendar(currentDate);
    });

    nextMonthBtn.addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        generateCalendar(currentDate);
    });

    // Sélection d'une date
    calendarDays.addEventListener('click', (e) => {
        const day = e.target.closest('.calendar-day5:not(.disabled)');
        if(!day) return;

        selectedDate = new Date(day.dataset.date);
        selectedDateInput.value = selectedDate.toLocaleDateString('fr-FR', {
            weekday: 'long',
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        }).replace(/^\w/, c => c.toUpperCase());
        
        // Réinitialiser l'heure sélectionnée
        selectedTime = null;
        selectedTimeInput.value = '';
        dateHeureInput.value = '';
        
        document.querySelectorAll('.calendar-day5').forEach(d => d.classList.remove('selected'));
        day.classList.add('selected');
        
        generateTimeSlots(selectedDate);
    });

    // Sélection d'un créneau horaire
    timeSlotsContainer.addEventListener('click', (e) => {
        const slot = e.target.closest('.time-slot5:not(.booked)');
        if(!slot) return;

        selectedTime = slot.dataset.time;
        selectedTimeInput.value = selectedTime;
        dateHeureInput.value = slot.dataset.datetime;
        
        document.querySelectorAll('.time-slot5').forEach(s => s.classList.remove('selected'));
        slot.classList.add('selected');
    });

    // Validation du formulaire
    rdvForm.addEventListener('submit', (e) => {
        if(!selectedDate || !selectedTime) {
            e.preventDefault();
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-danger5';
            alertDiv.textContent = 'Veuillez sélectionner une date et un horaire';
            rdvForm.prepend(alertDiv);
            
            setTimeout(() => {
                alertDiv.remove();
            }, 3000);
        }
    });

    // Initialisation
    generateCalendar(currentDate);
    
    // Afficher les créneaux pour aujourd'hui si c'est un jour valide
    const today = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
    const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
    if (today >= firstDay && today <= lastDay) {
        selectedDate = today;
        selectedDateInput.value = today.toLocaleDateString('fr-FR', {
            weekday: 'long',
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        }).replace(/^\w/, c => c.toUpperCase());
        
        generateTimeSlots(today);
    }
});
// Dans votre code existant, remplacez la fonction initMap par :
