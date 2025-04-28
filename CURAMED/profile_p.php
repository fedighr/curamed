<?php
session_start();
$conn=mysqli_connect("localhost","root","","curamed");
if(isset($_GET['id'])){
    $id=$_GET['id'];
}
else{
    $id=$_SESSION['user_id'];
}

$sql="SELECT u.*, p.group_sanguin , p.poids,p.taille,p.maladies_chroniques
                FROM utilisateur u 
                JOIN patient p ON u.id_utilisateur = p.id_patient 
                WHERE u.id_utilisateur = ".$id ;
if(!$res=mysqli_query($conn,$sql)){
    echo"error";
}

$table=mysqli_fetch_assoc($res);

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
    <script src="scripts/home.js"></script>
    <script src="scripts/profile_p.js" defer></script>
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

    <main class="profile-main container">
        <div class="profile-header mb-5">
            <div class="d-flex align-items-center gap-4">
                <div class="position-relative">
                    <img src="<?= $table['photo_profil'] ?>" class="profile-avatar" alt="Photo de profil">
                    <button class="btn btn-sm btn-primary avatar-edit-btn" onclick="document.getElementById('avatarInput').click()">
                        <i class="fas fa-camera"></i>
                    </button>
                    <input type="file" id="avatarInput" hidden accept="image/*">
                </div>
                <div>
                    <h1 class="profile-name"><?= $table['prenom'] ?> <?= $table['nom'] ?></h1>
                    <p class="text-muted mb-0">Membre depuis 2020</p>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <form id="profileForm" method="POST" onsubmit="return false" action="delete_account.php">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title mb-0"><i class="fas fa-user-circle me-2"></i>Informations personnelles</h3>
                    </div>
                    <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Prénom</label>
                                    <input type="text" class="form-control" name="prenom" value="<?= $table['prenom'] ?>" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nom</label>
                                    <input type="text" class="form-control" name="nom" value="<?= $table['nom'] ?>" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Age</label>
                                    <input type="number" class="form-control" name="age" value="<?= $table['age'] ?>" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Genre</label>
                                    <input type="text" class="form-control" name="genre" value="<?= $table['genre'] ?>" disabled>
                                </div>
                                <div class="col-12 text-end">
                                <button type="button" id="editBtn" class="btn btn-primary">
                                    <i class="fas fa-edit me-2"></i>Modifier
                                </button>
                                </div>
                                <div class="col-12 text-end mt-3">
                                <button type="button" class="btn btn-danger" 
                                        onclick="if(confirm('Êtes-vous sûr...')) { document.getElementById('profileForm').submit(); }">
                                    <i class="fas fa-trash me-2"></i>Supprimer le compte
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
                                        <p><?= $table['group_sanguin'] ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="medical-info-card">
                                    <i class="fas fa-ruler-vertical"></i>
                                    <div>
                                        <h6>Taille</h6>
                                        <p><?= $table['taille'] ?>CM</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="medical-info-card">
                                    <i class="fas fa-weight"></i>
                                    <div>
                                        <h6>Poids</h6>
                                        <p><?= $table['poids'] ?>KG</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="medical-info-card">
                                    <i class="fas fa-notes-medical"></i>
                                    <div>
                                        <h6>Maladies chroniques</h6>
                                        <p><?php if(empty($table['maladies_chroniques'])){echo "no maladies chronique";} else{echo($table['maladies_chroniques']);}?></p>
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
                                <a href="mailto:<?= $table['email'] ?>"><?= $table['email'] ?></a>
                            </li>
                            <li>
                                <i class="fas fa-phone"></i>
                                <a href="tel:<?= $table['telephone'] ?>"><?= $table['telephone'] ?></a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="card shadow-sm mt-4">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0"><i class="fas fa-calendar-check me-2"></i>Rendez-vous d'aujourd'hui</h3>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    <?php
                    $patientId = $_SESSION['user_id'];
                    $query = "SELECT r.id_rdv, r.date_heure, u.nom, u.prenom, f.continue_facture ,m.adresse_cabinet	
                    FROM rendez_vous r
                    JOIN medecin m ON r.id_medecin = m.id_medecin
                    JOIN utilisateur u ON m.id_medecin = u.id_utilisateur
                    JOIN paiement pe ON r.id_rdv = pe.id_rdv
                    LEFT JOIN facture f ON pe.id_paiement = f.id_paiement
                    WHERE r.id_patient = $patientId 
                    AND DATE(r.date_heure) = CURDATE()
                    AND r.date_heure > NOW()
                    AND r.statut = 'confirmé'
                    ORDER BY r.date_heure";
                    $result = mysqli_query($conn, $query);
                    
                    if(mysqli_num_rows($result) > 0): ?>
                        <div class="appointment-list">
                            <?php while($row = mysqli_fetch_assoc($result)): ?>
                                <div class="appointment-item mb-3 p-3 border rounded">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">Dr <?= htmlspecialchars($row['prenom']) ?> <?= htmlspecialchars($row['nom']) ?></h6>
                                            <small class="text-muted">
                                                <?= date('H:i', strtotime($row['date_heure'])) ?>
                                            </small>
                                            <small class="text-muted">
                                                <?= $row['adresse_cabinet'] ?>
                                            </small>

                                        </div>
                                        <div class="d-flex gap-2">
                                            <?php if(!empty($row['continue_facture'])): ?>
                                                <a href="<?= htmlspecialchars($row['continue_facture']) ?>" 
                                                class="btn btn-sm btn-success"
                                                target="_blank"
                                                download>
                                                    <i class="fas fa-download"></i> Fiche
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-3 text-muted">
                            Aucun rendez-vous prévu aujourd'hui
                        </div>
                    <?php endif; ?>
                    </div>
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
    <script src="scripts/profile_p.js"></script>
<?php mysqli_close($conn); ?>
</body>
</html>