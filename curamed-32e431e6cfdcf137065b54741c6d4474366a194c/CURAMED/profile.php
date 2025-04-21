<?php
session_start();

$conn = mysqli_connect("localhost", "root", "", "curamed");

if (!$conn) {
    die("Erreur de connexion à la base de données.");
}
if (isset($_GET['id'])) {
  $user_id = intval($_GET['id']);
} else {
  $user_id = $_SESSION['user_id'] ?? null;
} 

if (!$user_id) {
    die("Utilisateur non connecté.");
}

$req = "SELECT * FROM utilisateur WHERE id_utilisateur = $user_id";
$res = mysqli_query($conn, $req);

if ($res && mysqli_num_rows($res) > 0) {
    $table = mysqli_fetch_assoc($res);
} else {
    die("Erreur lors de la récupération des données utilisateur.");
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CuraMed - Prendre rendez-vous en ligne</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/home.css">
    <link rel="icon" type="image/png" sizes="32x32" href="images/logo.png">
    <script src="./scripts/home.js"></script>


</head>
<body>
    <!-- Header identique -->
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
                        <a href="settings.html" class="dropdown-item">
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

    <!-- Section Profil Médecin -->
    <section class="doctor-profile">
        <div class="container">
            <div class="profile-header">
                <div class="doctor-main-info">
                    <img src="images/marti.png" alt="Dr. bakhana" class="doctor-avatar">
                    <div class="doctor-meta">
                        <h1 class="doctor-name"><?php echo htmlspecialchars("DR ".$table["nom"]." ". $table["prenom"]);?></h1>
                        <p class="specialty"></p>
                        <div class="rating-badge">
                            <span class="rating">4.8</span>
                            <div class="stars">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                            <span class="reviews-count">(120 avis)</span>
                        </div>
                    </div>
                </div>
                <div class="doctor-contact">
                    <div class="contact-card">
                        <h3>Coordonnées</h3>
                        <ul class="contact-list">
                            <li><i class="fas fa-map-marker-alt"></i> 15 Rue de la Paix, 75002 Paris</li>
                            <li><i class="fas fa-phone"></i> 01 23 45 67 89</li>
                            <li><i class="fas fa-envelope"></i> contact@sarahdupont.com</li>
                        </ul>
                        <div class="social-links">
                            <a href="#"><i class="fab fa-linkedin"></i></a>
                            <a href="#"><i class="fab fa-doctolib"></i></a>
                            <a href="#"><i class="fas fa-globe"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Horaires de consultation -->
            <div class="consultation-section">
                <h2 class="section-title">Prendre rendez-vous</h2>
                
                <div class="availability-container">
                    <!-- Sélecteur de date -->
                    <div class="date-picker">
                        <div class="month-header">
                            <button class="nav-btn prev-month"><i class="fas fa-chevron-left"></i></button>
                            <h3>Septembre 2023</h3>
                            <button class="nav-btn next-month"><i class="fas fa-chevron-right"></i></button>
                        </div>
                        <div class="days-grid">
                            <!-- Généré dynamiquement en JS -->
                        </div>
                    </div>
                    
                    <!-- Créneaux horaires -->
                    <div class="time-slots">
                        <h4>Créneaux disponibles</h4>
                        <div class="slots-grid">
                            <button class="time-slot">09:00</button>
                            <button class="time-slot">09:30</button>
                            <button class="time-slot">10:00</button>
                            <button class="time-slot">10:30</button>
                            <button class="time-slot">11:00</button>
                            <!-- Plus de créneaux... -->
                        </div>
                    </div>
                </div>
                
                <!-- Formulaire de rendez-vous -->
                <form class="appointment-form">
                    <h3>Confirmation du rendez-vous</h3>
                    <div class="form-group">
                        <label>Date sélectionnée :</label>
                        <input type="text" class="selected-date" readonly>
                    </div>
                    <div class="form-group">
                        <label>Heure sélectionnée :</label>
                        <input type="text" class="selected-time" readonly>
                    </div>
                    <button type="submit" class="btn btn-primary btn-confirm">Confirmer le rendez-vous</button>
                </form>
            </div>

            <!-- Section Informations complémentaires -->
            <div class="doctor-details">
                <div class="about-section">
                    <h2>À propos</h2>
                    <p>Docteur en cardiologie avec plus de 15 ans d'expérience. Spécialisée dans les troubles du rythme cardiaque et la prévention cardiovasculaire.</p>
                    <div class="qualifications">
                        <h3>Formations & Certifications</h3>
                        <ul>
                            <li>Diplôme de Cardiologie - Université Paris Descartes</li>
                            <li>Certificat de Rythmologie - Collège Français de Cardiologie</li>
                        </ul>
                    </div>
                </div>
                <div class="location-map">
                    <h2>Localisation</h2>
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2624.9916256937595!2d2.292292615509614!3d48.8583700792875!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e66e2964e34e2d%3A0x8ddca9ee380ef7e0!2sTour%20Eiffel!5e0!3m2!1sfr!2sfr!4v1623258137807!5m2!1sfr!2sfr" 
                            class="map-iframe" 
                            allowfullscreen="" 
                            loading="lazy"></iframe>
                </div>
                
            </div>
        </div>
    </section>

    <!-- Footer identique -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <img src="images\logo3.png" alt="CuraMed" class="footer-logo">
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
    <script src="scripts/profile.js"></script>

    <table class="table table-bordered table-striped bg-white shadow">
      <tbody>
        <tr>
          <th>ID</th>
          <td><?php echo htmlspecialchars($table["id_utilisateur"]); ?></td>
        </tr>
        <tr>
          <th>Prénom</th>
          <td><?php echo htmlspecialchars($table["prenom"]); ?></td>
        </tr>
        <tr>
          <th>Nom</th>
          <td><?php echo htmlspecialchars($table["nom"]); ?></td>
        </tr>
        <tr>
          <th>Âge</th>
          <td><?php echo htmlspecialchars($table["age"]); ?></td>
        </tr>
        <tr>
          <th>Email</th>
          <td><?php echo htmlspecialchars($table["email"]); ?></td>
        </tr>
        <tr>
          <th>Telephone</th>
          <td><?php echo htmlspecialchars($table["telephone"]); ?></td>
        </tr>
        <tr>
          <th>Type</th>
          <td><?php echo htmlspecialchars($table["type_utilisateur"]); ?></td>
        </tr>
      </tbody>
    </table>
  </div>
</body>
</html>