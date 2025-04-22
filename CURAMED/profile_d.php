<?php
session_start();
// Données simulées du médecin
$_SESSION['medecin'] = [
    'photo' => 'images/marti.png',
    'nom' => 'bakhana',
    'prenom' => 'amira',
    'specialite' => 'Dermatologie',
    'experience' => '15 ans',
    'adresse' => '18 Rue des Médecins, 69000 Sousse',
    'telephone' => '00000000',
    'email' => 'dr.bakhana@clinique.fr',
    'horaires' => 'Lun-Ven: 8h-19h',
    'tarif' => '60dt'
];
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
</head>
<body>
   <!-- Header -->
   <header class="header">
        <nav class="nav-container container">
            <a href="home.php" class="logo-link">
                <img src="images/logo.png" alt="CuraMed" class="logo-img">
            </a>
            
            <div class="nav-links">
                <a href="doctors.html" class="nav-icon" title="Médecins">
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
                        <a href="profile_p.php" class="dropdown-item">
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
            </div>
            <button class="mobile-menu-btn d-lg-none">
                <i class="fas fa-bars"></i>
            </button>
        </nav>
    </header>

    <main class="doctor-main container">
        <div class="doctor-header mb-5">
            <div class="d-flex align-items-center gap-4">
                <img src="<?= $_SESSION['medecin']['photo'] ?>" class="doctor-avatar" alt="Photo du médecin">
                <div>
                    <h1 class="doctor-name">Dr. <?= $_SESSION['medecin']['prenom'] ?> <?= $_SESSION['medecin']['nom'] ?></h1>
                    <div class="doctor-specialty"><?= $_SESSION['medecin']['specialite'] ?></div>
                    <div class="doctor-experience"><i class="fas fa-award"></i> <?= $_SESSION['medecin']['experience'] ?> d'expérience</div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Colonne gauche - Informations et agenda -->
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h3><i class="fas fa-calendar-alt me-2"></i>Agenda</h3>
                    </div>
                    <div class="card-body">
                        <div class="calendar-tools mb-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="btn-group">
                                    <button class="btn btn-outline-primary" id="prevWeek"><i class="fas fa-chevron-left"></i></button>
                                    <button class="btn btn-outline-primary" id="nextWeek"><i class="fas fa-chevron-right"></i></button>
                                </div>
                                <span id="currentWeek" class="fw-bold"></span>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAppointmentModal">
                                    <i class="fas fa-plus me-2"></i>Nouveau RDV
                                </button>
                            </div>
                        </div>
                        
                        <div class="calendar-grid" id="calendar"></div>
                    </div>
                </div>

                <div class="card shadow-sm mt-4">
                    <div class="card-header bg-primary text-white">
                        <h3><i class="fas fa-users me-2"></i>Patients</h3>
                    </div>
                    <div class="card-body">
                        <div class="patients-search mb-3">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Rechercher un patient...">
                                <button class="btn btn-primary"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                        
                        <div class="patients-list">
                            <div class="patient-item">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="patient-info">
                                        <h5>Marie Dupont</h5>
                                        <div class="patient-meta">
                                            <span class="badge bg-primary">2 RDV à venir</span>
                                            <span class="text-muted">Dernière visite: 15/03/2024</span>
                                        </div>
                                    </div>
                                    <div class="patient-actions">
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> Dossier
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <!-- Plus de patients... -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Colonne droite - Statistiques et actions rapides -->
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h3><i class="fas fa-chart-line me-2"></i>Statistiques</h3>
                    </div>
                    <div class="card-body">
                        <div class="stats-grid">
                            <div class="stat-item">
                                <i class="fas fa-calendar-check"></i>
                                <div class="stat-value">18</div>
                                <div class="stat-label">RDV cette semaine</div>
                            </div>
                            <div class="stat-item">
                                <i class="fas fa-user-clock"></i>
                                <div class="stat-value">2h15</div>
                                <div class="stat-label">Temps moyen/RDV</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mt-4">
                    <div class="card-header bg-primary text-white">
                        <h3><i class="fas fa-bolt me-2"></i>Actions rapides</h3>
                    </div>
                    <div class="card-body">
                        <div class="quick-actions">
                            <button class="btn btn-action">
                                <i class="fas fa-file-prescription"></i>
                                <span>Rédiger ordonnance</span>
                            </button>
                            <button class="btn btn-action">
                                <i class="fas fa-comment-medical"></i>
                                <span>Envoyer message</span>
                            </button>
                            <button class="btn btn-action">
                                <i class="fas fa-file-medical"></i>
                                <span>Ajouter au dossier</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Nouveau RDV -->
        <div class="modal fade" id="addAppointmentModal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="fas fa-calendar-plus me-2"></i>Nouveau rendez-vous</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="appointmentForm">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Patient</label>
                                    <select class="form-select">
                                        <option>Marie Dupont</option>
                                        <!-- Plus de patients -->
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Type de consultation</label>
                                    <select class="form-select">
                                        <option>Consultation standard</option>
                                        <option>Suivi de traitement</option>
                                        <option>Urgence</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Date et heure</label>
                                    <input type="datetime-local" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Durée</label>
                                    <select class="form-select">
                                        <option>15 min</option>
                                        <option>30 min</option>
                                        <option>45 min</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Notes</label>
                                    <textarea class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="button" class="btn btn-primary">Enregistrer</button>
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