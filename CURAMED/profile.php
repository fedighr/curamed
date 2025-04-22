<?php
session_start();

$conn = mysqli_connect("localhost", "root", "", "curamed");

if (!$conn) {
    die("Erreur de connexion à la base de données.");
}

// Récupération de l'ID du médecin
if (isset($_GET['id'])) {
    $medecin_id = intval($_GET['id']);
} else {
    die("Aucun médecin spécifié.");
}

// Récupération des infos du médecin
$req_medecin = "SELECT u.*, m.specialite, m.adresse_cabinet, m.experience 
                FROM utilisateur u 
                JOIN medecin m ON u.id_utilisateur = m.id_medecin 
                WHERE u.id_utilisateur = ".$medecin_id;
$res_medecin = mysqli_query($conn, $req_medecin);

if ($res_medecin && mysqli_num_rows($res_medecin) > 0) {
    $medecin = mysqli_fetch_assoc($res_medecin);
} else {
    die("Médecin non trouvé.");
}

// Récupération des rendez-vous existants
$rdv_existants = [];
$req_rdv = "SELECT date_heure FROM rendez_vous WHERE id_medecin = $medecin_id AND statut = 'confirmé'";
$res_rdv = mysqli_query($conn, $req_rdv);
while ($row = mysqli_fetch_assoc($res_rdv)) {
    $rdv_existants[] = $row['date_heure'];
}

// Traitement du formulaire
$erreur = $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['prendre_rdv'])) {
    if (!isset($_SESSION['user_id'])) {
        $erreur = "Vous devez être connecté pour prendre un rendez-vous.";
    } else {
        $patient_id = $_SESSION['user_id'];
        $date_heure = $_POST['date_heure'];
        
        $date_obj = new DateTime($date_heure);
        $now = new DateTime();
        
        if ($date_obj < $now) {
            $erreur = "Vous ne pouvez pas prendre de rendez-vous dans le passé.";
        } elseif (in_array($date_heure, $rdv_existants)) {
            $erreur = "Ce créneau est déjà pris, veuillez en choisir un autre.";
        } else {
            $insert_rdv = "INSERT INTO rendez_vous (id_patient, id_medecin, date_heure, statut) 
                          VALUES ($patient_id, $medecin_id, '$date_heure', 'en attente')";
            
            if (mysqli_query($conn, $insert_rdv)) {
                $success = "Votre rendez-vous a été pris avec succès!";
                
                // Ajouter une notification
                $message_notif = "Nouveau RDV avec Dr. " . $medecin['nom'] . " le " . $date_obj->format('d/m/Y à H:i');
                $insert_notif = "INSERT INTO notification (id_utilisateur, message, date_envoi, type, statut) 
                                VALUES ($patient_id, '$message_notif', NOW(), 'email', 'envoyé')";
                mysqli_query($conn, $insert_notif);
            } else {
                $erreur = "Erreur lors de la prise de rendez-vous: " . mysqli_error($conn);
            }
        }
    }
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CuraMed - Dr. <?php echo htmlspecialchars($medecin['nom']); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="styles/home.css">
    <link rel="icon" type="image/png" sizes="32x32" href="images/logo.png">
    <script src="scripts/profile.js"></script>
    <script src="scripts/home.js"></script>

</head>
<body>
    <!-- Header (inchangé) -->
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

    <!-- Section Profil Médecin (modifiée) -->
<section class="doctor-profile-section5">
    <div class="doctor-container5">
        <?php if ($erreur): ?>
            <div class="alert alert-danger5"><?php echo $erreur; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success5"><?php echo $success; ?></div>
        <?php endif; ?>

        <div class="doctor-header5">
    <div class="doctor-main-info5">
        <img src="<?php echo htmlspecialchars($medecin['photo_profil'] ?: 'images/default-doctor.png'); ?>" alt="Dr. <?php echo htmlspecialchars($medecin['nom']); ?>" class="doctor-avatar-img5">
        <div class="doctor-meta-info5">
            <h1 class="doctor-full-name5"><?php echo htmlspecialchars("DR ".$medecin["nom"]." ". $medecin["prenom"]);?></h1>
            <?php if (!empty($medecin['specialite'])): ?>
                <p class="doctor-specialty5"><?php echo htmlspecialchars($medecin['specialite']); ?></p>
            <?php endif; ?>
        </div>
    </div>
            <div class="doctor-contact-box5">
                <div class="doctor-contact-card5">
                    <h3>Coordonnées</h3>
                    <ul class="doctor-contact-list5">
                        <li><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($medecin['adresse_cabinet']); ?></li>
                        <li><i class="fas fa-phone"></i> <?php echo htmlspecialchars($medecin['telephone']); ?></li>
                        <li><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($medecin['email']); ?></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Prise de rendez-vous -->
        <div class="appointment-section5">
            <h2 class="appointment-title5">Prendre rendez-vous</h2>
            
            <div class="appointment-availability5">
                <!-- Calendrier -->
                <div class="appointment-calendar5">
                    <div class="calendar-header5">
                        <button class="calendar-nav5 prev5"><i class="fas fa-chevron-left"></i></button>
                        <h3 id="calendar-current-month5">Chargement...</h3>
                        <button class="calendar-nav5 next5"><i class="fas fa-chevron-right"></i></button>
                    </div>
                    <div class="calendar-days5" id="calendar-days5">
                        <!-- Généré par JS -->
                    </div>
                </div>
                
                <!-- Créneaux horaires -->
                <div class="appointment-time-slots5">
                    <h4>Créneaux disponibles</h4>
                    <div class="time-slots-grid5" id="time-slots15">
                        <!-- Généré par JS -->
                    </div>
                </div>
            </div>
            
            <!-- Formulaire de confirmation -->
            <form class="appointment-form5" method="POST" id="rdv-form5">
                <h3>Confirmation du rendez-vous</h3>
                <div class="form-group5">
                    <label>Date sélectionnée :</label>
                    <input type="text" class="form-selected-date5" id="selected-date5" readonly>
                </div>
                <div class="form-group5">
                    <label>Heure sélectionnée :</label>
                    <input type="text" class="form-selected-time5" id="selected-time5" readonly>
                </div>
                <input type="hidden" name="date_heure" id="date-heure5">
                <button type="submit" class="btn btn-primary btn-confirm-appointment5" name="prendre_rdv">Confirmer le rendez-vous</button>
            </form>
        </div>

        <!-- Informations complémentaires -->
      
            <div class="doctor-about5">
                <h2>Experience</h2>
                <p><?php echo htmlspecialchars($medecin['experience']); ?></p>
            </div>
            
    

        </div>
    </div>
</section>


    <!-- Footer (inchangé) -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <img src="images\Untitled-1.png" alt="CuraMed" class="footer-logo">
                    <p>La solution simple et efficace pour prendre rendez-vous avec des professionnels de santé.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                
                <div class="footer-col">
                    <h4>Patients</h4>
                    <ul>
                        <li><a href="#">Trouver un médecin</a></li>
                        <li><a href="#">Consultation en ligne</a></li>
                        <li><a href="#">Fonctionnalités</a></li>
                        <li><a href="#">Tarifs</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h4>Professionnels</h4>
                    <ul>
                        <li><a href="#">Solutions pour médecins</a></li>
                        <li><a href="#">Tarifs</a></li>
                        <li><a href="#">Témoignages</a></li>
                        <li><a href="#">Nous rejoindre</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h4>Entreprise</h4>
                    <ul>
                        <li><a href="#">À propos</a></li>
                        <li><a href="#">Carrières</a></li>
                        <li><a href="#">Presse</a></li>
                        <li><a href="#">Contact</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <div class="footer-links">
                    <a href="#">Mentions légales</a>
                    <a href="#">Politique de confidentialité</a>
                    <a href="#">Conditions générales</a>
                    <a href="#">Cookies</a>
                </div>
                <div class="copyright">
                    © 2025 CuraMed. Tous droits réservés.
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=VOTRE_CLE_API&callback=initMap" async defer></script>
    

   
  </div>
</body>
</html>