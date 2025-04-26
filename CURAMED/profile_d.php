<?php
session_start();
$conn = mysqli_connect("localhost","root","","curamed");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if(isset($_GET['id'])){
    $id=$_GET['id'];
}
else{
    $id=$_SESSION['user_id'];
}
$req = "SELECT u.*, m.specialite, m.adresse_cabinet, m.experience,m.ville
        FROM utilisateur u
        JOIN medecin m ON u.id_utilisateur = m.id_medecin
        WHERE u.id_utilisateur = $id";
$res = mysqli_query($conn, $req);
$doctorInfo = mysqli_fetch_assoc($res);

function timeOptions($selected = '') {
    $start = strtotime('08:00');
    $end = strtotime('18:00');
    $selected = substr($selected, 0, 5);
    while($start <= $end) {
        $time = date('H:i', $start);
        $sel = ($time === $selected) ? 'selected' : '';
        echo "<option value='$time' $sel>$time</option>";
        $start = strtotime('+30 minutes', $start);
    }
}



$jours = [1=>'Lundi',2=>'Mardi',3=>'Mercredi',4=>'Jeudi',5=>'Vendredi',6=>'Samedi',7=>'Dimanche'];
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    mysqli_query($conn, "DELETE FROM calendrier_medecin WHERE id_medecin = $id");
    foreach ($jours as $i => $j) {
        if (isset($_POST['off'][$i])) continue;
        $hd = $_POST['debut'][$i] ?? '';
        $pd = $_POST['pause_debut'][$i] ?? '';
        $pe = $_POST['pause_fin'][$i] ?? '';
        $hf = $_POST['fin'][$i] ?? '';
        if ($hd && $hf) {
            $hdi = mysqli_real_escape_string($conn, $hd);
            $pdi = mysqli_real_escape_string($conn, $pd);
            $pei = mysqli_real_escape_string($conn, $pe);
            $hfi = mysqli_real_escape_string($conn, $hf);
            mysqli_query($conn, "INSERT INTO calendrier_medecin
                (id_medecin,jour,heure_debut,pause_debut,pause_fin,heure_fin)
                VALUES
                ($id,'$j','$hdi','$pdi','$pei','$hfi')");
        }
    }
    $message = "Calendrier mis à jour.";
}

$template = [];
$res_tpl = mysqli_query($conn, "SELECT jour, heure_debut, pause_debut, pause_fin, heure_fin 
    FROM calendrier_medecin WHERE id_medecin = $id");
while ($r = mysqli_fetch_assoc($res_tpl)) {
    $template[$r['jour']] = $r;
}

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
    <script src="./scripts/home.js"></script>
    <script src="./scripts/profile_d.js"></script>
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

<main class="profile-main container">
    <div class="profile-header mb-5">
        <div class="d-flex align-items-center gap-4">
            <div class="position-relative">
                <img src="<?= htmlspecialchars($doctorInfo['photo_profil'] ?? 'images/default-doctor.jpg') ?>" 
                     class="profile-avatar" alt="Photo de profil">
            </div>
            <div>
                <h1 class="profile-name">Dr. <?= htmlspecialchars($doctorInfo['prenom']) ?> <?= htmlspecialchars($doctorInfo['nom']) ?></h1>
                <p class="text-muted mb-1 d-flex align-items-center">
                <i class="fas fa-user-md me-2 text-primary"></i><?= $doctorInfo['specialite'] ?>
                </p>
                <p class="text-muted mb-1 d-flex align-items-center">
                <i class="fas fa-map-marker-alt me-2 text-danger"></i><?= $doctorInfo['ville'] ?>
                </p>
                <p class="text-muted mb-0 d-flex align-items-center">
                <i class="fas fa-clinic-medical me-2 text-success"></i><?= $doctorInfo['adresse_cabinet'] ?>
                </p>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Column -->
        <div class="row g-4">
            <div class="col-lg-8">
                <form id="profileForm" method="POST" onsubmit="return false">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title mb-0"><i class="fas fa-user-circle me-2"></i>Informations personnelles</h3>
                    </div>
                    <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Prénom</label>
                                    <input type="text" class="form-control" name="prenom" value="<?= $doctorInfo['prenom'] ?>" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nom</label>
                                    <input type="text" class="form-control" name="nom" value="<?= $doctorInfo['nom'] ?>" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Age</label>
                                    <input type="number" class="form-control" name="age" value="<?= $doctorInfo['age'] ?>" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Genre</label>
                                    <input type="text" class="form-control" name="genre" value="<?= $doctorInfo['genre'] ?>" disabled>
                                </div>
                                <div class="col-12 text-end">
                                <button type="button" id="editBtn" class="btn btn-primary">
                                    <i class="fas fa-edit me-2"></i>Modifier
                                </button>
                                </div>
                                <div class="col-12 text-end mt-3">
                                    <button type="submit" name="delete_account" class="btn btn-danger" 
                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer votre compte? Cette action est irréversible!');">
                                        <i class="fas fa-trash me-2"></i>Supprimer le compte
                                    </button>
                                </div>
                            </div>
                    </div>
                </div>
                </form>


            <div class="card shadow-sm mt-4" id="planning">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0"><i class="fas fa-calendar-alt me-2"></i>Mon planning</h3>
                </div>
                <div class="card-body">
            <?php if ($message): ?>
                <div class="alert alert-success d-flex align-items-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    <?= htmlspecialchars($message) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            
            <form method="post">
            <div class="table-responsive">
                <table class="table calendar-table">
                    <thead>
                        <tr>
                            <th>Jour</th>
                            <th>Off</th>
                            <th>Début</th>
                            <th>Pause début</th>
                            <th>Pause fin</th>
                            <th>Fin</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($jours as $i => $j): 
                            $dayData = $template[$j] ?? [];
                            $isOff = !isset($template[$j]);
                        ?>
                        <tr data-day="<?= $i ?>">
                            <td class="weekday-label"><?= $j ?></td>
                            
                            <td class="text-center">
                                <input type="checkbox" 
                                    name="off[<?= $i ?>]" 
                                    class="off-checkbox" 
                                    <?= $isOff ? 'checked' : '' ?>>
                            </td>
                            
                            <td>
                                <select name="debut[<?= $i ?>]" class="form-select time-select no-arrow">
                                    <option value="">--</option>
                                    <?php timeOptions($dayData['heure_debut'] ?? ''); ?>
                                </select>
                            </td>
                            
                            <td>
                                <select name="pause_debut[<?= $i ?>]" class="form-select time-select no-arrow">
                                    <option value="">--</option>
                                    <?php timeOptions($dayData['pause_debut'] ?? ''); ?>
                                </select>
                            </td>
                            
                            <td>
                                <select name="pause_fin[<?= $i ?>]" class="form-select time-select no-arrow">
                                    <option value="">--</option>
                                    <?php timeOptions($dayData['pause_fin'] ?? ''); ?>
                                </select>
                            </td>
                            
                            <td>
                                <select name="fin[<?= $i ?>]" class="form-select time-select no-arrow">
                                    <option value="">--</option>
                                    <?php timeOptions($dayData['heure_fin'] ?? ''); ?>
                                </select>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
                    <button class="save-btn">
                        <i class="fas fa-save"></i>
                        Sauvegarder les modifications
                    </button>
                </form>
</div>
            </div>
        </div>


        <div class="col-lg-4">

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0"><i class="fas fa-address-book me-2"></i>Contact</h3>
                </div>
                <div class="card-body">
                    <ul class="contact-list">
                        <li>
                            <i class="fas fa-envelope"></i>
                            <a href="mailto:<?= $doctorInfo['email'] ?>"><?= $doctorInfo['email'] ?></a>
                        </li>
                        <li>
                            <i class="fas fa-phone"></i>
                            <a href="tel:<?= $doctorInfo['telephone'] ?>"><?= $doctorInfo['telephone'] ?></a>
                        </li>
                    </ul>
                </div>
            </div>


            <div class="card shadow-sm mt-4">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0"><i class="fas fa-calendar-check me-2"></i>Rendez-vous</h3>
                </div>
                <div class="card-body">
                    <div class="emergency-contacts">

                        <div class="emergency-contact mb-3">
                            <div class="view-mode">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6>Dr. Bakhana</h6>
                                        <p class="text-muted mb-0">Contact principal - 06 12 34 56 78</p>
                                    </div>
                                    <button class="btn btn-sm btn-outline-primary edit-btn">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </div>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const tpl = <?= json_encode($tpl_js) ?>;
const labels = ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'];
let currentDate = new Date();
function getWeekNumber(d){
    const date=new Date(d.getFullYear(),d.getMonth(),d.getDate());
    date.setHours(0,0,0,0);
    date.setDate(date.getDate()+4-(date.getDay()||7));
    const yearStart=new Date(date.getFullYear(),0,1);
    return Math.ceil((((date-yearStart)/86400000)+1)/7);
}
function updateCalendar(){
    const cal=document.getElementById('calendar'); cal.innerHTML='';
    const day=currentDate.getDay()||7;
    const monday=new Date(currentDate);
    monday.setDate(currentDate.getDate()-day+1);
    for(let i=0;i<7;i++){
        const d=new Date(monday);
        d.setDate(monday.getDate()+i);
        const dow=labels[d.getDay()];
        const slot=tpl.find(r=>r.jour===dow);
        let hours='Le médecin ne travaille pas';
        if(slot){ hours=`${slot.heure_debut.slice(0,5)}–${slot.pause_debut.slice(0,5)}<br>${slot.pause_fin.slice(0,5)}–${slot.heure_fin.slice(0,5)}`; }
        cal.innerHTML+=`<div class="calendar-cell"><div class="calendar-day-header">${d.toLocaleDateString('fr-FR',{weekday:'short',day:'numeric'})}</div><div class="hours">${hours}</div></div>`;
    }
    document.getElementById('currentWeekRange').textContent=`Semaine ${getWeekNumber(currentDate)} – ${currentDate.getFullYear()}`;
}
document.getElementById('prevWeek').onclick=()=>{currentDate.setDate(currentDate.getDate()-7);updateCalendar();};
document.getElementById('nextWeek').onclick=()=>{currentDate.setDate(currentDate.getDate()+7);updateCalendar();};
document.getElementById('currentWeekBtn').onclick=()=>{currentDate=new Date();updateCalendar();};
document.addEventListener('DOMContentLoaded',updateCalendar);
</script>
</body>
</html>