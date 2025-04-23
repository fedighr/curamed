<?php
session_start();
// Données du patient (simulées pour l'exemple)
$_SESSION['patient'] = [
    'photo' => 'images/profil.jpg',
    'nom' => 'chakroun',
    'prenom' => 'abdelhedi',
    'email' => 'abdelhedi.chakroun@example.com',
    'telephone' => '97370975',
    'notifications' => true,
    'newsletter' => false,
    'theme' => 'light'
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CuraMed - Paramètres</title>
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
                <img src="images/untitled-1.png" alt="CuraMed" class="logo-img">
            </a>
            
            <div class="nav-links">
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

    <main class="settings-main container">
        <div class="settings-header mb-5">
            <h1 class="settings-title"><i class="fas fa-cog me-3"></i>Paramètres</h1>
            <nav class="settings-nav">
                <div class="nav nav-tabs" id="settings-tabs" role="tablist">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#account-tab">
                        <i class="fas fa-user-circle me-2"></i>Compte
                    </button>
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#notifications-tab">
                        <i class="fas fa-bell me-2"></i>Notifications
                    </button>
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#security-tab">
                        <i class="fas fa-shield-alt me-2"></i>Sécurité
                    </button>
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#appearance-tab">
                        <i class="fas fa-palette me-2"></i>Apparence
                    </button>
                </div>
            </nav>
        </div>

        <div class="tab-content" id="settings-tabs-content">
            <!-- Onglet Compte -->
            <div class="tab-pane fade show active" id="account-tab">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title mb-0"><i class="fas fa-user-edit me-2"></i>Informations du compte</h3>
                    </div>
                    <div class="card-body">
                        <form id="accountForm">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Adresse email</label>
                                    <input type="email" class="form-control" value="<?= $_SESSION['patient']['email'] ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Téléphone</label>
                                    <input type="tel" class="form-control" value="<?= $_SESSION['patient']['telephone'] ?>">
                                </div>
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="newsletterCheck" <?= $_SESSION['patient']['newsletter'] ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="newsletterCheck">
                                            Recevoir la newsletter
                                        </label>
                                    </div>
                                </div>
                                <div class="col-12 text-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Enregistrer
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Onglet Notifications -->
            <div class="tab-pane fade" id="notifications-tab">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title mb-0"><i class="fas fa-bell me-2"></i>Préférences de notifications</h3>
                    </div>
                    <div class="card-body">
                        <form id="notificationsForm">
                            <div class="mb-4">
                                <h5 class="mb-3"><i class="fas fa-comment-medical me-2"></i>Rendez-vous</h5>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="appointmentReminders" checked>
                                    <label class="form-check-label" for="appointmentReminders">Rappels de rendez-vous</label>
                                </div>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="appointmentConfirmations" checked>
                                    <label class="form-check-label" for="appointmentConfirmations">Confirmations de rendez-vous</label>
                                </div>
                            </div>

                            <div class="mb-4">
                                <h5 class="mb-3"><i class="fas fa-envelope me-2"></i>Messages</h5>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="newMessages" checked>
                                    <label class="form-check-label" for="newMessages">Nouveaux messages</label>
                                </div>
                            </div>

                            <div class="mb-4">
                                <h5 class="mb-3"><i class="fas fa-bullhorn me-2"></i>Actualités</h5>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="healthTips" <?= $_SESSION['patient']['notifications'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="healthTips">Conseils santé</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="promotions" <?= $_SESSION['patient']['newsletter'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="promotions">Offres spéciales</label>
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Enregistrer
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Onglet Sécurité -->
            <div class="tab-pane fade" id="security-tab">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title mb-0"><i class="fas fa-shield-alt me-2"></i>Sécurité du compte</h3>
                    </div>
                    <div class="card-body">
                        <form id="securityForm">
                            <div class="mb-4">
                                <h5 class="mb-3"><i class="fas fa-key me-2"></i>Changer le mot de passe</h5>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label">Mot de passe actuel</label>
                                        <input type="password" class="form-control" placeholder="Entrez votre mot de passe actuel">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Nouveau mot de passe</label>
                                        <input type="password" class="form-control" placeholder="Créez un nouveau mot de passe">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Confirmer le mot de passe</label>
                                        <input type="password" class="form-control" placeholder="Confirmez le nouveau mot de passe">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <h5 class="mb-3"><i class="fas fa-lock me-2"></i>Authentification à deux facteurs</h5>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="twoFactorAuth">
                                    <label class="form-check-label" for="twoFactorAuth">Activer l'authentification à deux facteurs</label>
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Enregistrer
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Onglet Apparence -->
            <div class="tab-pane fade" id="appearance-tab">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title mb-0"><i class="fas fa-palette me-2"></i>Préférences d'affichage</h3>
                    </div>
                    <div class="card-body">
                        <form id="appearanceForm">
                            <div class="mb-4">
                                <h5 class="mb-3"><i class="fas fa-moon me-2"></i>Thème</h5>
                                <div class="theme-selector">
                                    <input type="radio" name="theme" id="lightTheme" value="light">
                                    <label for="lightTheme" class="theme-option">
                                        <i class="fas fa-sun"></i>
                                        <button id="theme-toggle" class="btn btn-outline-primary">Claire</button>
                                    </label>
                                    
                                    <input type="radio" name="theme" id="darkTheme" value="dark" >
                                    <label for="darkTheme" class="theme-option">
                                        <i class="fas fa-moon"></i>
                                        <button id="theme-toggle" class="btn btn-outline-primary">Dark Mode</button>
                                    </label>
                                    
                                    <input type="radio" name="theme" id="systemTheme" value="system">
                                    <label for="systemTheme" class="theme-option">
                                        <i class="fas fa-desktop"></i>
                                        <button id="theme-toggle" class="btn btn-outline-primary">System</button>
                                    </label>
                                </div>
                            </div>

                            <div class="mb-4">
                                <h5 class="mb-3"><i class="fas fa-text-height me-2"></i>Taille du texte</h5>
                                <div class="range-slider">
                                    <input type="range" class="form-range" min="0" max="2" step="1" id="textSize" value="1">
                                    <div class="range-labels">
                                        <span>Petit</span>
                                        <span>Moyen</span>
                                        <span>Grand</span>
                                    </div>
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Enregistrer
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <img src="images/logo.png" alt="CuraMed" class="footer-logo">
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
    <script src="scripts/settings.js"></script>
</body>
</html>