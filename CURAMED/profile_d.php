<?php
session_start();
$conn=mysqli_connect("localhost","root","","curamed");
// Données simulées du médecin
$id=$_SESSION['user_id'];
$req_rdv = "SELECT u.*, m.specialite, m.adresse_cabinet, m.experience 
                FROM utilisateur u 
                JOIN medecin m ON u.id_utilisateur = m.id_medecin 
                WHERE u.id_utilisateur = ".$id ;
$res_rdv = mysqli_query($conn, $req_rdv);
$doctorInfo= mysqli_fetch_assoc($res_rdv);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CuraMed - Profil Médecin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/home.css">
    <link rel="icon" type="image/png" sizes="32x32" href="images/logo.png">
    <script> src="scripts/profile_d.js"></script>
    <script> src="scripts/home.js"></script>
</head>
<body>
   <!-- Header -->
   <!-- Header -->
   <header class="header">
        <nav class="nav-container container">
            <a href="home.php" class="logo-link">
                <img src="images/logo.png" alt="CuraMed" class="logo-img">
            </a>
            <div class="nav-links">
                <?php if(isset($_SESSION['user_id'])) :?>
                <a href="search.php" class="nav-icon" title="Médecins">
                    <i class="fas fa-user-md"></i>
                </a>
                
                <div class="nav-icon notifications-wrapper" title="Notifications">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">0</span>
                    <div class="notifications-dropdown">
                        <div class="notification-header">
                        </div>
                        <div class="notification-list"></div>
                    </div>
                </div>
                <a href="appointments.html" class="nav-icon" title="Rendez-vous">
                    <i class="fas fa-calendar-alt"></i>
                </a>
                
                <div class="profile-dropdown">
                    <img src="<?php echo ($_SESSION['photo']); ?>" class="profile-image" alt="">
                    <div class="dropdown-menu">
                        <?php if(isset($_SESSION['type']) && $_SESSION['type'] =='patient') : ?>
                            <a href="profile_p.php" class="dropdown-item">
                        <?php else :?>
                            <a href="profile_d.php" class="dropdown-item">
                        <?php endif;?>
                            <i class="fas fa-user"></i> Mon profil
                        </a>
                        <a href="settings.php" class="dropdown-item">
                            <i class="fas fa-cog"></i> Paramètres
                        </a>
                        <?php
                         if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin') : ?>
                            <a href="dashboard.php" class="dropdown-item">
                                <i class="fas fa-th-large"></i> Tableau de bord
                            </a>
                        <?php endif; ?>
                        <a href="logout.php" class="dropdown-item">
                            <i class="fas fa-sign-out-alt"></i> Se déconnecter
                        </a>
                    </div>
                </div>
                <button class="mobile-menu-btn d-lg-none">
                    <i class="fas fa-bars"></i>
                </button>
                <?php else : ?>
                <div class="container mt-5 text-center">
                    <a href="login.html" class="btn btn-custom mx-2">Connexion</a>
                    <a href="signup.html" class="btn btn-custom mx-2">S'inscrire</a>
                </div>
            </div>
        </nav>
        <?php endif ;?>
    </header>

    <main class="doctor-main container">
    <div class="doctor-header mb-5">
        <div class="d-flex align-items-center gap-4">
            <img src="<?= htmlspecialchars($doctorInfo['photo_profil'] ?? 'images/default-doctor.jpg') ?>" class="doctor-avatar rounded-circle" alt="Photo du médecin" style="width: 120px; height: 120px; object-fit: cover;">
            <div>
                <h1 class="doctor-name">Dr. <?= htmlspecialchars($doctorInfo['prenom']) ?> <?= htmlspecialchars($doctorInfo['nom']) ?></h1>
                <div class="doctor-specialty badge bg-primary"><?= htmlspecialchars($doctorInfo['specialite']) ?></div>
                <div class="doctor-experience text-muted mt-2">
                    <i class="fas fa-award"></i> <?= htmlspecialchars($doctorInfo['experience']) ?> d'expérience
                </div>
                <div class="doctor-address mt-2">
                    <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($doctorInfo['adresse_cabinet']) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Colonne gauche - Informations et agenda -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Agenda</h3>
                </div>
                <div class="card-body">
                    <div class="calendar-tools mb-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="btn-group">
                                <button class="btn btn-outline-primary" id="prevWeek"><i class="fas fa-chevron-left"></i></button>
                                <button class="btn btn-outline-primary" id="nextWeek"><i class="fas fa-chevron-right"></i></button>
                                <button class="btn btn-outline-primary" id="currentWeekBtn">Cette semaine</button>
                            </div>
                            <span id="currentWeekRange" class="fw-bold"></span>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAppointmentModal">
                                <i class="fas fa-plus me-2"></i>Nouveau RDV
                            </button>
                        </div>
                    </div>
                    
                    <div class="calendar-container">
                        <div class="calendar-header d-flex">
                            <?php 
                            $days = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
                            foreach ($days as $day): ?>
                                <div class="calendar-day-header flex-grow-1 text-center py-2 fw-bold"><?= $day ?></div>
                            <?php endforeach; ?>
                        </div>
                        <div class="calendar-grid" id="calendar">
                            <!-- Calendar will be populated by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mt-4">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0"><i class="fas fa-users me-2"></i>Patients récents</h3>
                </div>
                <div class="card-body">
                    <div class="patients-search mb-3">
                        <div class="input-group">
                            <input type="text" class="form-control" id="patientSearch" placeholder="Rechercher un patient...">
                            <button class="btn btn-primary"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                    
                    <div class="patients-list">
                        <?php if (empty($patients)): ?>
                            <div class="text-center py-4 text-muted">
                                Aucun patient trouvé
                            </div>
                        <?php else: ?>
                            <?php foreach ($patients as $patient): ?>
                                <div class="patient-item border-bottom py-3">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <img src="<?= htmlspecialchars($patient['photo_profil'] ?? 'images/default-patient.jpg') ?>" 
                                                 class="rounded-circle me-3" 
                                                 width="50" 
                                                 height="50" 
                                                 alt="Photo patient"
                                                 style="object-fit: cover;">
                                            <div class="patient-info">
                                                <h5 class="mb-1"><?= htmlspecialchars($patient['prenom']) ?> <?= htmlspecialchars($patient['nom']) ?></h5>
                                                <div class="patient-meta">
                                                    <span class="badge bg-primary"><?= $patient['appointment_count'] ?> RDV</span>
                                                    <span class="text-muted ms-2">Dernière visite: <?= date('d/m/Y', strtotime($patient['last_visit'])) ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="patient-actions">
                                            <button class="btn btn-sm btn-outline-primary view-patient" data-id="<?= $patient['id_utilisateur'] ?>">
                                                <i class="fas fa-eye"></i> Dossier
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Colonne droite - Statistiques et actions rapides -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0"><i class="fas fa-chart-line me-2"></i>Statistiques</h3>
                </div>
                <div class="card-body">
                    <div class="stats-grid row">
                        <div class="stat-item col-6 text-center py-3">
                            <i class="fas fa-calendar-check fa-2x mb-2 text-primary"></i>
                            <div class="stat-value"><?= $doctorStats['total_appointments'] ?? 0 ?></div>
                            <div class="stat-label">RDV cette semaine</div>
                        </div>
                        <div class="stat-item col-6 text-center py-3">
                            <i class="fas fa-user-clock fa-2x mb-2 text-primary"></i>
                            <div class="stat-value"><?= round(($doctorStats['avg_duration'] ?? 30) / 60, 2) ?>h</div>
                            <div class="stat-label">Temps moyen/RDV</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mt-4">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0"><i class="fas fa-bolt me-2"></i>Actions rapides</h3>
                </div>
                <div class="card-body">
                    <div class="quick-actions">
                        <button class="btn btn-action btn-light w-100 mb-3 p-3 rounded-3">
                            <i class="fas fa-file-prescription fa-2x mb-2 text-primary"></i>
                            <span class="d-block">Rédiger ordonnance</span>
                        </button>
                        <button class="btn btn-action btn-light w-100 mb-3 p-3 rounded-3">
                            <i class="fas fa-comment-medical fa-2x mb-2 text-primary"></i>
                            <span class="d-block">Envoyer message</span>
                        </button>
                        <button class="btn btn-action btn-light w-100 p-3 rounded-3">
                            <i class="fas fa-file-medical fa-2x mb-2 text-primary"></i>
                            <span class="d-block">Ajouter au dossier</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mt-4">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0"><i class="fas fa-clock me-2"></i>Prochains RDV</h3>
                </div>
                <div class="card-body">
                    <?php if (empty($appointments)): ?>
                        <div class="text-center py-2 text-muted">
                            Aucun rendez-vous à venir
                        </div>
                    <?php else: ?>
                        <ul class="list-group">
                            <?php foreach ($appointments as $appointment): ?>
                                <li class="list-group-item border-0 py-2 px-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong><?= htmlspecialchars($appointment['patient_prenom']) ?> <?= htmlspecialchars($appointment['patient_nom']) ?></strong>
                                            <div class="text-muted small">
                                                <?= date('d/m/Y H:i', strtotime($appointment['date_heure'])) ?>
                                            </div>
                                        </div>
                                        <span class="badge bg-info">
                                            <?= date('H:i', strtotime($appointment['date_heure'])) ?>
                                        </span>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Nouveau RDV -->
    <div class="modal fade" id="addAppointmentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-calendar-plus me-2"></i>Nouveau rendez-vous</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="appointmentForm" action="save_appointment.php" method="POST">
                        <input type="hidden" name="doctor_id" value="<?= $doctorId ?>">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Patient</label>
                                <select class="form-select" name="patient_id" required>
                                    <option value="">Sélectionner un patient</option>
                                    <?php foreach ($patients as $patient): ?>
                                        <option value="<?= $patient['id_utilisateur'] ?>">
                                            <?= htmlspecialchars($patient['prenom']) ?> <?= htmlspecialchars($patient['nom']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Type de consultation</label>
                                <select class="form-select" name="consultation_type" required>
                                    <option value="standard">Consultation standard</option>
                                    <option value="followup">Suivi de traitement</option>
                                    <option value="emergency">Urgence</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date et heure</label>
                                <input type="datetime-local" class="form-control" name="appointment_datetime" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Durée</label>
                                <select class="form-select" name="duration" required>
                                    <option value="15">15 min</option>
                                    <option value="30" selected>30 min</option>
                                    <option value="45">45 min</option>
                                    <option value="60">1 heure</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Notes</label>
                                <textarea class="form-control" name="notes" rows="3"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" form="appointmentForm" class="btn btn-primary">Enregistrer</button>
                </div>
            </div>
        </div>
    </div>
</main>

    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <img src="images/logo3.png" alt="CuraMed" class="footer-logo">
                    <p>La solution simple et efficace pour prendre rendez-vous avec des professionnels de santé.</p>
                </div>
                <div class="footer-col">
                    <h4>Patients</h4>
                    <ul>
                        <li><a href="#">Trouver un médecin</a></li>
                        <li><a href="#">Consultation en ligne</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Entreprise</h4>
                    <ul>
                        <li><a href="#">À propos</a></li>
                        <li><a href="#">Contact</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <div class="copyright">
                    © 2025 CuraMed. Tous droits réservés.
                </div>
            </div>
        </div>
    </footer>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="scripts/profile_d.js"></script>
</body>
</html>