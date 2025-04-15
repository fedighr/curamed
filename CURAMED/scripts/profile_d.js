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