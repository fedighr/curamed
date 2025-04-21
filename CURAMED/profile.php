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
                WHERE u.id_utilisateur = $medecin_id";
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
<<<<<<< HEAD
=======
    <script src="./scripts/home.js"></script>


>>>>>>> 32e431e6cfdcf137065b54741c6d4474366a194c
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
                </div>
                
                <a href="appointments.html" class="nav-icon" title="Rendez-vous">
                    <i class="fas fa-calendar-alt"></i>
                </a>
                
                <div class="profile-dropdown">
                    <img src="<?php echo htmlspecialchars($_SESSION['photo'] ?? 'images/default-profile.png'); ?>" class="profile-image" alt="Profil">
                </div>
            </div>
        </nav>
    </header>

    <!-- Section Profil Médecin -->
    <section class="doctor-profile">
        <div class="container">
            <?php if ($erreur): ?>
                <div class="alert alert-danger"><?php echo $erreur; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <div class="profile-header">
                <div class="doctor-main-info">
                    <img src="<?php echo htmlspecialchars($medecin['photo_profil'] ?: 'images/default-doctor.png'); ?>" alt="Dr. <?php echo htmlspecialchars($medecin['nom']); ?>" class="doctor-avatar">
                    <div class="doctor-meta">
<<<<<<< HEAD
                        <h1 class="doctor-name">Dr. <?php echo htmlspecialchars($medecin['prenom'] . ' ' . $medecin['nom']); ?></h1>
                        <p class="specialty"><?php echo htmlspecialchars($medecin['specialite']); ?></p>
=======
                        <h1 class="doctor-name"><?php echo htmlspecialchars("DR ".$table["nom"]." ". $table["prenom"]);?></h1>
                        <p class="specialty"></p>
>>>>>>> 32e431e6cfdcf137065b54741c6d4474366a194c
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
                            <li><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($medecin['adresse_cabinet']); ?></li>
                            <li><i class="fas fa-phone"></i> <?php echo htmlspecialchars($medecin['telephone']); ?></li>
                            <li><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($medecin['email']); ?></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Prise de rendez-vous -->
            <div class="consultation-section">
                <h2 class="section-title">Prendre rendez-vous</h2>
                
                <div class="availability-container">
                    <!-- Calendrier -->
                    <div class="date-picker">
                        <div class="month-header">
                            <button class="nav-btn prev-month"><i class="fas fa-chevron-left"></i></button>
                            <h3 id="current-month">Chargement...</h3>
                            <button class="nav-btn next-month"><i class="fas fa-chevron-right"></i></button>
                        </div>
                        <div class="days-grid" id="calendar-days">
                            <!-- Généré par JS -->
                        </div>
                    </div>
                    
                    <!-- Créneaux horaires -->
                    <div class="time-slots">
                        <h4>Créneaux disponibles</h4>
                        <div class="slots-grid" id="time-slots">
                            <!-- Généré par JS -->
                        </div>
                    </div>
                </div>
                
                <!-- Formulaire de confirmation -->
                <form class="appointment-form" method="POST" id="rdv-form">
                    <h3>Confirmation du rendez-vous</h3>
                    <div class="form-group">
                        <label>Date sélectionnée :</label>
                        <input type="text" class="selected-date" id="selected-date" readonly>
                    </div>
                    <div class="form-group">
                        <label>Heure sélectionnée :</label>
                        <input type="text" class="selected-time" id="selected-time" readonly>
                    </div>
                    <input type="hidden" name="date_heure" id="date-heure">
                    <button type="submit" class="btn btn-primary btn-confirm" name="prendre_rdv">Confirmer le rendez-vous</button>
                </form>
            </div>

            <!-- Informations complémentaires -->
            <div class="doctor-details">
                <div class="about-section">
                    <h2>À propos</h2>
                    <p><?php echo htmlspecialchars($medecin['experience']); ?></p>
                </div>
                
                <div class="location-map">
                    <h2>Localisation</h2>
                    <div id="map" style="height: 400px; width: 100%;"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-bottom">
                <div class="copyright">
                    © 2025 CuraMed. Tous droits réservés.
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=VOTRE_CLE_API&callback=initMap" async defer></script>
    <script src="scripts/profile.js"></script>
<<<<<<< HEAD
=======

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
>>>>>>> 32e431e6cfdcf137065b54741c6d4474366a194c
</body>
</html>