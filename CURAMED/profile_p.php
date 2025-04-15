<?php
session_start();
// Données du patient (simulées pour l'exemple)
$_SESSION['patient'] = [
    'photo' => 'images/profil.jpg',
    'nom' => 'chakroun',
    'prenom' => 'abdelhedi',
    'email' => 'abdelhedi.chakroun@example.com',
    'telephone' => '97370975',
    'adresse' => 'jawhara, 75001 sousse',
    'naissance' => '2004-07-11',
    'genre' => 'r3ad',
    'groupe_sanguin' => 'A+',
    'medecin_traitant' => 'Dr. bakhana'
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CuraMed - Mon Profil</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/home.css">
    <link rel="icon" type="image/png" sizes="32x32" href="images/logo.png">
</head>
<body>
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

    <main class="profile-main container">
        <div class="profile-header mb-5">
            <div class="d-flex align-items-center gap-4">
                <div class="position-relative">
                    <img src="<?= $_SESSION['patient']['photo'] ?>" class="profile-avatar" alt="Photo de profil">
                    <button class="btn btn-sm btn-primary avatar-edit-btn" onclick="document.getElementById('avatarInput').click()">
                        <i class="fas fa-camera"></i>
                    </button>
                    <input type="file" id="avatarInput" hidden accept="image/*">
                </div>
                <div>
                    <h1 class="profile-name"><?= $_SESSION['patient']['prenom'] ?> <?= $_SESSION['patient']['nom'] ?></h1>
                    <p class="text-muted mb-0">Membre depuis 2020</p>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title mb-0"><i class="fas fa-user-circle me-2"></i>Informations personnelles</h3>
                    </div>
                    <div class="card-body">
                        <form id="profileForm">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Prénom</label>
                                    <input type="text" class="form-control" value="<?= $_SESSION['patient']['prenom'] ?>" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nom</label>
                                    <input type="text" class="form-control" value="<?= $_SESSION['patient']['nom'] ?>" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Date de naissance</label>
                                    <input type="date" class="form-control" value="<?= $_SESSION['patient']['naissance'] ?>" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Genre</label>
                                    <input type="text" class="form-control" value="<?= $_SESSION['patient']['genre'] ?>" disabled>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Adresse</label>
                                    <input type="text" class="form-control" value="<?= $_SESSION['patient']['adresse'] ?>" disabled>
                                </div>
                                <div class="col-12 text-end">
                                    <button type="button" id="editBtn" class="btn btn-primary">
                                        <i class="fas fa-edit me-2"></i>Modifier
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm mt-4">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title mb-0"><i class="fas fa-file-medical me-2"></i>Informations médicales</h3>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="medical-info-card">
                                    <i class="fas fa-tint"></i>
                                    <div>
                                        <h6>Groupe sanguin</h6>
                                        <p><?= $_SESSION['patient']['groupe_sanguin'] ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="medical-info-card">
                                    <i class="fas fa-user-md"></i>
                                    <div>
                                        <h6>Médecin traitant</h6>
                                        <p><?= $_SESSION['patient']['medecin_traitant'] ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title mb-0"><i class="fas fa-address-book me-2"></i>Contacts</h3>
                    </div>
                    <div class="card-body">
                        <ul class="contact-list">
                            <li>
                                <i class="fas fa-envelope"></i>
                                <a href="mailto:<?= $_SESSION['patient']['email'] ?>"><?= $_SESSION['patient']['email'] ?></a>
                            </li>
                            <li>
                                <i class="fas fa-phone"></i>
                                <a href="tel:<?= $_SESSION['patient']['telephone'] ?>"><?= $_SESSION['patient']['telephone'] ?></a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="card shadow-sm mt-4">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title mb-0"><i class="fas fa-shield-alt me-2"></i>Contacts d'urgence</h3>
                    </div>
                    <div class="card-body">
                        <div class="emergency-contacts">
                            <div class="emergency-contact mb-3">
                                <div class="view-mode">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6> Dr bakhana </h6>
                                            <p class="text-muted mb-0">Père - 06 12 34 56 78</p>
                                        </div>
                                        <button class="btn btn-sm btn-outline-primary edit-btn">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="edit-mode d-none">
                                    <form class="contact-form">
                                        <div class="row g-2">
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" value="Jean Dupont">
                                            </div>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" value="Père">
                                            </div>
                                            <div class="col-12">
                                                <input type="tel" class="form-control" value="06 12 34 56 78">
                                            </div>
                                            <div class="col-12 text-end">
                                                <button type="button" class="btn btn-secondary cancel-btn">Annuler</button>
                                                <button type="submit" class="btn btn-primary">Enregistrer</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <button id="addContactBtn" class="btn btn-primary w-100">
                            <i class="fas fa-plus me-2"></i>Ajouter un contact
                        </button>
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
    <script src="scripts/profile_p.js"></script>
</body>
</html>