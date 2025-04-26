<?php
session_start();

require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$conn = mysqli_connect("localhost", "root", "", "curamed");

if (!$conn) {
    die("Erreur de connexion à la base de données.");
}
$_SESSION['id_medecin'] = '';
if (isset($_GET['id'])) {
    $medecin_id = intval($_GET['id']);
} else {
    die("Aucun médecin spécifié.");
}



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
function isTimeInPast($date, $time) {
    $currentDateTime = new DateTime();
    $slotDateTime = new DateTime($date . ' ' . $time);
    return $slotDateTime < $currentDateTime;
}

$rdv_existants = [];
$req_rdv = "SELECT DATE_FORMAT(date_heure, '%Y-%m-%d %H:%i') as dt FROM rendez_vous WHERE id_medecin = $medecin_id";
$res_rdv = mysqli_query($conn, $req_rdv);
while ($row = mysqli_fetch_assoc($res_rdv)) {
    $rdv_existants[] = $row['dt'];
}
if(!isset($_SESSION['messagee'])){
    $_SESSION['messagee'] = '';
}
$message = $_SESSION['messagee'];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['prendre_rdv'])) {
    if (!isset($_SESSION['user_id'])) {
        $erreur = "Vous devez être connecté pour prendre un rendez-vous.";
    } else {
        $patient_id = $_SESSION['user_id'];
        $date_heure = $_POST['date_heure'];
        
        $date_obj = new DateTime($date_heure);
        $now = new DateTime();
        if (!$schedule) {
            $erreur = "Ce médecin n'a pas défini ses horaires de disponibilité.";}
        
        if ($date_obj < $now) {
            $erreur = "Vous ne pouvez pas prendre de rendez-vous dans le passé.";
        } elseif (in_array($date_heure, $rdv_existants)) {
            $erreur = "Ce créneau est déjà pris, veuillez en choisir un autre.";
        } else {

            $verification_code = rand(1000, 9999);
            $_SESSION['verification_code'] = $verification_code;
            $_SESSION['id_medecin'] = $medecin_id;
            $_SESSION['date'] = $date_heure ;
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'topfadighribi11@gmail.com';
                $mail->Password   = 'skbqackvjratqaxn';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;
                
                $mail->setFrom('topfadighribi11@gmail.com', 'Curamed');
                $mail->addAddress($_SESSION['user_email'], $_SESSION['prenom'] . ' ' . $_SESSION['nom']);
                $mail->isHTML(true);
                $mail->Subject = 'Votre code de confirmation';
                $mail->Body    = 'Votre code de confirmation est : <b>' . $verification_code . '</b>';
                $mail->send();


                header("Location: Paiement.php");
                exit();
            } catch (Exception $e) {
                die("L'email n'a pas pu être envoyé. Erreur: " . $mail->ErrorInfo);
            }
        }
    }
}

$selectedDate = $_GET['date'] ?? date('Y-m-d');
$dayOfWeek = date('N', strtotime($selectedDate));
$jours = [1=>'Lundi',2=>'Mardi',3=>'Mercredi',4=>'Jeudi',5=>'Vendredi',6=>'Samedi',7=>'Dimanche'];

$scheduleQuery = "SELECT * FROM calendrier_medecin 
                 WHERE id_medecin = $medecin_id 
                 AND jour = '".$jours[$dayOfWeek]."'";
$scheduleResult = mysqli_query($conn, $scheduleQuery);
$schedule = $scheduleResult ? mysqli_fetch_assoc($scheduleResult) : null;


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
    <script src="./scripts/home.js"></script>
    <script src="./scripts/profile.js"></script>
</head>
<body>
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
                    <?php
                    $conn = mysqli_connect("localhost", "root", "", "curamed");
                    $user_id = intval($_SESSION['user_id'] ?? 0);
                    
                    $stmt = mysqli_prepare($conn, 
                        "SELECT COUNT(*) as nb FROM notification WHERE id_utilisateur = ?");
                    mysqli_stmt_bind_param($stmt, "i", $user_id);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    $row = mysqli_fetch_assoc($result);
                    ?>
                    <span class="notification-badge"><?= htmlspecialchars($row['nb']) ?></span>
    
            <div class="notifications-dropdown">
                <div class="notification-header">
                    <h5>Notifications</h5>
                </div>
                <div class="notification-list">
                    <?php
                    $stmt = mysqli_prepare($conn, 
                        "SELECT id_rdv,id_notification, message FROM notification 
                        WHERE id_utilisateur = ?");
                    mysqli_stmt_bind_param($stmt, "i", $user_id);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    
                    while ($row = mysqli_fetch_assoc($result)) :
                    ?>
                    <div class="notification-item">
                        <form method="POST" action="confirmation.php">
                            <input type="hidden" name="notification_id" 
                                value="<?= htmlspecialchars($row['id_notification']) ?>">
                            <?php if(isset($_SESSION['type']) && $_SESSION['type'] == 'patient'): ?>   
                            <button type="submit" 
                                    name="dismiss_notification" 
                                    class="notification-close-btn" 
                                    title="Fermer la notification">&times;</button>
                            
                            <?php endif; ?>
                            <p><?= htmlspecialchars($row['message']) ?></p>
                            <?php if(isset($_SESSION['type']) && $_SESSION['type'] == 'medecin'): ?>
                            <div class="action-buttons">
                                <button type="submit" name="accepter" 
                                        class="icon-btn fas fa-check text-success" 
                                        title="Accepter"></button>
                                <button type="submit" name="refuser" 
                                        class="icon-btn fas fa-times text-danger" 
                                        title="Refuser"></button>
                            </div>
                            <?php endif; ?>
                        </form>
                        </div>
                    <?php endwhile; ?>
                </div>
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
                        <a href="historique.php" class="dropdown-item">
                            <i class="fas fa-cog"></i> historique
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

    <section class="doctor-profile-section5">
        <div class="doctor-container5">        
            <?php if (!empty($message)): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= $_SESSION['messagee']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
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

            <div class="appointment-section5">
                <h2 class="appointment-title5">Prendre rendez-vous</h2>
                
                <div class="appointment-availability5">
                <div class="appointment-calendar5">
                    <div class="calendar-header5">
                        <input type="date" id="appointment-date" 
                            class="form-control"
                            min="<?= date('Y-m-d') ?>" 
                            value="<?= $selectedDate ?>"
                            onchange="updateTimeSlots(this.value)">
                    </div>
                </div>
                                    
                    <div class="appointment-time-slots5">
                        <h4>Créneaux disponibles</h4>
                        <div class="time-slots-grid5" id="time-slots15">
                        <?php 
                            if ($schedule): 
                                $start = strtotime($schedule['heure_debut']);
                                $end = strtotime($schedule['heure_fin']);
                                $pauseStart = strtotime($schedule['pause_debut']);
                                $pauseEnd = strtotime($schedule['pause_fin']);
                                
                                $current = $start;
                                $availableSlots = 0;
                                
                                while ($current < $end):
                                    if ($current >= $pauseStart && $current < $pauseEnd) {
                                        $current = strtotime('+30 minutes', $current);
                                        continue;
                                    }
                                    
                                    $time = date('H:i', $current);
                                    $datetime = date('Y-m-d H:i', strtotime($selectedDate . ' ' . $time));
                                    
                                    // Skip if time is in the past for today
                                    if ($selectedDate == date('Y-m-d') && isTimeInPast($selectedDate, $time)) {
                                        $current = strtotime('+30 minutes', $current);
                                        continue;
                                    }
                                    
                                    if (!in_array($datetime, $rdv_existants)):
                                        $availableSlots++;
                            ?>
                                        <div class="time-slot" 
                                            data-time="<?= $time ?>" 
                                            onclick="selectTime(this)">
                                            <?= $time ?>
                                        </div>
                            <?php 
                                    endif;
                                    $current = strtotime('+30 minutes', $current);
                                endwhile;
                                
                                if ($availableSlots === 0): ?>
                                    <div class="alert alert-info">
                                        <?= ($selectedDate == date('Y-m-d')) ? 
                                            "Tous les créneaux disponibles pour aujourd'hui sont passés ou réservés" : 
                                            "Tous les créneaux sont réservés pour cette journée" ?>
                                    </div>
                            <?php 
                                endif;
                            else: ?>
                                <div class="alert alert-info">
                                    Aucun horaire disponible pour cette journée
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <form class="appointment-form5" method="POST" id="rdv-form5">
                    <h3>Confirmation du rendez-vous</h3>
                    <div class="form-group5">
                        <label>Date sélectionnée :</label>
                        <input type="text" class="form-selected-date5" 
                               id="selected-date" value="<?= $selectedDate ?>" readonly>
                    </div>
                    <div class="form-group5">
                        <label>Heure sélectionnée :</label>
                        <input type="text" class="form-selected-time5" 
                               id="selected-time" readonly>
                    </div>
                    <input type="hidden" name="date_heure" id="date-heure5">
                    <button type="submit" class="btn btn-primary btn-confirm-appointment5" 
                            name="prendre_rdv">Confirmer le rendez-vous</button>
                </form>
            </div>
        </div>
    </section>

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
    <script>
    function updateTimeSlots(date) {
        window.location.href = `?id=<?= $medecin_id ?>&date=${date}`;
    }

    function selectTime(element) {
        document.querySelectorAll('.time-slot').forEach(slot => {
            slot.classList.remove('selected');
        });
        element.classList.add('selected');
        
        const date = document.getElementById('selected-date').value;
        const time = element.getAttribute('data-time');
        document.getElementById('selected-time').value = time;
        document.getElementById('date-heure5').value = `${date} ${time}:00`;
    }
    </script>
</body>
</html>
<?php mysqli_close($conn);?>