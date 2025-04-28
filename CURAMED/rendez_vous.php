
<?php
session_start();
$conn=mysqli_connect("localhost","root","","curamed");

if(!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['type'];

$query = "";
if($user_type == 'patient') {
    $query = "SELECT r.*, u.nom AS medecin_nom, u.prenom AS medecin_prenom 
              FROM rendez_vous r 
              JOIN medecin m ON r.id_medecin = m.id_medecin 
              JOIN utilisateur u ON m.id_medecin = u.id_utilisateur 
              WHERE r.id_patient = ?";
} else {
    $query = "SELECT r.*, u.nom AS patient_nom, u.prenom AS patient_prenom 
              FROM rendez_vous r 
              JOIN patient p ON r.id_patient = p.id_patient 
              JOIN utilisateur u ON p.id_patient = u.id_utilisateur 
              WHERE r.id_medecin = ?";
}

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['annuler'])) {
    $rdv_id = $_POST['rdv_id'];
    $update_stmt = $conn->prepare("UPDATE rendez_vous SET statut = 'annulé' WHERE id_rdv = ?");
    $update_stmt->bind_param("i", $rdv_id);
    $update_stmt->execute();
    header("Refresh:0");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CuraMed</title>
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

                <a href="rendez_vous.php" class="nav-icon" title="Rendez-vous">
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
                        <i class="fas fa-history"></i> historique
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
    <section><h1>hhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhh</h1></section>
    <section><h3>.</h3></section>
    <section class="dashboard-main5">
        <div class="container5">
            <div class="section-header5">
                <h2>Mes Rendez-vous </h2>
            </div>

            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="future-tab" data-bs-toggle="tab" 
                            data-bs-target="#future" type="button" role="tab" 
                            aria-controls="future" aria-selected="true">
                        À venir
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="past-tab" data-bs-toggle="tab" 
                            data-bs-target="#past" type="button" role="tab" 
                            aria-controls="past" aria-selected="false">
                        Passés
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="canceled-tab" data-bs-toggle="tab" 
                            data-bs-target="#canceled" type="button" role="tab" 
                            aria-controls="canceled" aria-selected="false">
                        Annulés
                    </button>
                </li>
            </ul>

            <div class="tab-content mt-4">

                <div class="tab-pane fade show active" id="future" role="tabpanel" aria-labelledby="future-tab">
                    <div class="table-responsive5">
                        <table class="calendar-table5">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th><?= $user_type == 'patient' ? 'Médecin' : 'Patient' ?></th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($rdv = $res->fetch_assoc()):
                                    $date = new DateTime($rdv['date_heure']);
                                    if($date > new DateTime() && $rdv['statut'] != 'annulé'): ?>
                                    <tr>
                                        <td><?= $date->format('d/m/Y H:i') ?></td>
                                        <td>
                                            <?php if($user_type == 'patient'): ?>
                                                Dr. <?= $rdv['medecin_prenom'] ?> <?= $rdv['medecin_nom'] ?>
                                            <?php else: ?>
                                                <?= $rdv['patient_prenom'] ?> <?= $rdv['patient_nom'] ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="status-badge <?= $rdv['statut'] == 'confirmé' ? 'success' : 'warning' ?>">
                                                <?= $rdv['statut'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if($rdv['statut'] == 'confirmé'): ?>
                                                <form method="POST" onsubmit="return confirm('Annuler ce rendez-vous ?')">
                                                    <input type="hidden" name="rdv_id" value="<?= $rdv['id_rdv'] ?>">
                                                    <button type="submit" name="annuler" class="btn-outline-primary5">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endif; endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Onglet Rendez-vous passés -->
                <div class="tab-pane fade" id="past" role="tabpanel" aria-labelledby="past-tab">
                    <div class="table-responsive5">
                        <table class="calendar-table5">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th><?= $user_type == 'patient' ? 'Médecin' : 'Patient' ?></th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $res->data_seek(0);
                                while($rdv = $res->fetch_assoc()):
                                    $date = new DateTime($rdv['date_heure']);
                                    if($date <= new DateTime() && $rdv['statut'] != 'annulé'): ?>
                                    <tr>
                                        <td><?= $date->format('d/m/Y H:i') ?></td>
                                        <td>
                                            <?php if($user_type == 'patient'): ?>
                                                Dr. <?= $rdv['medecin_prenom'] ?> <?= $rdv['medecin_nom'] ?>
                                            <?php else: ?>
                                                <?= $rdv['patient_prenom'] ?> <?= $rdv['patient_nom'] ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>Terminé</td>
                                    </tr>
                                <?php endif; endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="canceled" role="tabpanel" aria-labelledby="canceled-tab">
                    <div class="table-responsive5">
                        <table class="calendar-table5">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th><?= $user_type == 'patient' ? 'Médecin' : 'Patient' ?></th>
                                    <th>Raison</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $res->data_seek(0);
                                while($rdv = $res->fetch_assoc()):
                                    if($rdv['statut'] == 'annulé'): ?>
                                    <tr>
                                        <td><?= (new DateTime($rdv['date_heure']))->format('d/m/Y H:i') ?></td>
                                        <td>
                                            <?php if($user_type == 'patient'): ?>
                                                Dr. <?= $rdv['medecin_prenom'] ?> <?= $rdv['medecin_nom'] ?>
                                            <?php else: ?>
                                                <?= $rdv['patient_prenom'] ?> <?= $rdv['patient_nom'] ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>Annulé par <?= $rdv['statut'] == 'patient' ? 'vous' :  'le medecin'?></td>
                                    </tr>
                                <?php endif; endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
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
</body>
</html>
<?php mysqli_close($conn); ?>