<?php
session_start();

// Connexion avec MySQLi
$conn = mysqli_connect("localhost", "root", "", "curamed");

if (!$conn) {
    die("Erreur de connexion à la base de données: " . mysqli_connect_error());
}

// Initialisation des variables de tri
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'nom';

// Récupérer les critères de recherche
$searchTerm = isset($_POST['cherche']) ? trim($_POST['cherche']) : '';
$specialite = isset($_POST['specialite']) ? trim($_POST['specialite']) : '';
$ville = isset($_POST['ville']) ? trim($_POST['ville']) : '';

// Préparation de la requête SQL
$sql = "SELECT u.*, m.specialite, m.adresse_cabinet, m.experience,
               AVG(h.notes_medecin) as note_moyenne, COUNT(h.id_consultation) as nombre_avis
        FROM utilisateur u
        JOIN medecin m ON u.id_utilisateur = m.id_medecin
        LEFT JOIN historique h ON m.id_medecin = h.id_medecin
        WHERE u.type_utilisateur = 'medecin'";

if (!empty($searchTerm)) {
    $sql .= " AND (u.nom LIKE ? OR u.prenom LIKE ? OR m.adresse_cabinet LIKE ?)";
}

if (!empty($specialite)) {
    $sql .= " AND m.specialite = ?";
}

if (!empty($ville)) {
    $sql .= " AND m.adresse_cabinet LIKE ?";
}

$sql .= " GROUP BY u.id_utilisateur";

// Préparation de la requête
$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    die("Erreur de préparation de la requête: " . mysqli_error($conn));
}

// Liaison des paramètres
if (!empty($searchTerm) || !empty($specialite) || !empty($ville)) {
    $types = '';
    $params = [];
    
    if (!empty($searchTerm)) {
        $types .= 'sss';
        $searchParam = "%$searchTerm%";
        array_push($params, $searchParam, $searchParam, $searchParam);
    }
    
    if (!empty($specialite)) {
        $types .= 's';
        array_push($params, $specialite);
    }
    
    if (!empty($ville)) {
        $types .= 's';
        $villeParam = "%$ville%";
        array_push($params, $villeParam);
    }
    
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

// Exécution
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$medecins = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Récupération des spécialités pour le filtre
$specialites_result = mysqli_query($conn, "SELECT DISTINCT specialite FROM medecin ORDER BY specialite");
$specialites = [];
while ($row = mysqli_fetch_assoc($specialites_result)) {
    $specialites[] = $row['specialite'];
}
mysqli_free_result($specialites_result);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultats de recherche - CuraMed</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
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
                <?php if(isset($_SESSION['user_id'])) : ?>
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
                    <img src="<?php echo isset($_SESSION['photo']) ? htmlspecialchars($_SESSION['photo']) : 'images/default-profile.jpg'; ?>" class="profile-image" alt="Photo de profil">
                    <div class="dropdown-menu">
                        <?php if(isset($_SESSION['type_utilisateur']) && $_SESSION['type_utilisateur'] == 'patient') : ?>
                            <a href="profile_p.php" class="dropdown-item">
                        <?php else : ?>
                            <a href="profile_d.php" class="dropdown-item">
                        <?php endif; ?>
                            <i class="fas fa-user"></i> Mon profil
                        </a>
                        <a href="settings.php" class="dropdown-item">
                            <i class="fas fa-cog"></i> Paramètres
                        </a>
                        <a href="historique.php" class="dropdown-item">
                            <i class="fas fa-cog"></i> historique
                        </a>
                        <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin') : ?>
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
                <?php endif; ?>
            </div>
        </nav>
    </header>
   
    <!-- Section des résultats -->
    <section class="search-results" style="margin-top: 100px; padding: 2rem 0;">
        <div class="container">
            <h1 class="mb-4">Résultats de recherche</h1>
            
            <!-- Affichage des critères de recherche -->
            <div class="search-criteria mb-4 p-3 bg-light rounded">
                <h5>Votre recherche :</h5>
                <?php if(!empty($searchTerm)): ?>
                    <span class="badge bg-primary me-2">"<?= htmlspecialchars($searchTerm) ?>"</span>
                <?php endif; ?>
                <?php if(!empty($specialite)): ?>
                    <span class="badge bg-secondary me-2">Spécialité : <?= htmlspecialchars($specialite) ?></span>
                <?php endif; ?>
                <?php if(!empty($ville)): ?>
                    <span class="badge bg-success">Ville : <?= htmlspecialchars($ville) ?></span>
                <?php endif; ?>
            </div>
            
            <!-- Nombre de résultats et options de tri -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <p><?= count($medecins) ?> médecin(s) trouvé(s)</p>
                <div class="sort-options">
                    <span class="me-2">Trier par :</span>
                    <span class="sort-option <?= $sort === 'nom' ? 'active' : '' ?>" data-sort="nom">Nom</span>
                    <span class="sort-option mx-2 <?= $sort === 'prenom' ? 'active' : '' ?>" data-sort="prenom">Prénom</span>
                    <span class="sort-option mx-2 <?= $sort === 'specialite' ? 'active' : '' ?>" data-sort="specialite">Spécialité</span>
                    <span class="sort-option <?= $sort === 'note_moyenne' ? 'active' : '' ?>" data-sort="note_moyenne">Note</span>
                </div>
            </div>
            
            <!-- Liste des médecins -->
            <div class="doctors-list">
                <?php if(count($medecins) > 0): ?>
                    <div class="row">
                        <?php foreach($medecins as $medecin): ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card doctor-card h-100">
                                    <div class="card-body">
                                    <div class="d-flex align-items-start mb-3">
                                    <div class="doctor-avatar-container">
                                                <img src="<?= htmlspecialchars($medecin['photo_profil'] ?: 'images/default-doctor.jpg') ?>" 
                                                     alt="Photo du Dr. <?= htmlspecialchars($medecin['prenom'] . ' ' . $medecin['nom']) ?>" 
                                                     class="doctor-avatar">
                                            </div>
                                            <div>
                                                <h4 class="card-title">Dr. <?= htmlspecialchars($medecin['prenom'] . ' ' . $medecin['nom']) ?></h4>
                                                <span class="badge bg-info text-dark"><?= htmlspecialchars($medecin['specialite']) ?></span>
                                                <p class="small text-muted mb-1">Expérience : <?= htmlspecialchars($medecin['experience']) ?></p>
                                            </div>
                                        </div>
                                    </div>
                                        
                                        <div class="doctor-info mb-3">
                                            <p><i class="fas fa-map-marker-alt me-2"></i> <?= htmlspecialchars($medecin['adresse_cabinet']) ?></p>
                                            <p><i class="fas fa-phone me-2"></i> <?= htmlspecialchars($medecin['telephone']) ?></p>
                                            
                                            <!-- Affichage de la note moyenne -->
                                            <?php if($medecin['note_moyenne']): ?>
                                                <div class="rating-stars">
                                                    <?php 
                                                    $fullStars = floor($medecin['note_moyenne']);
                                                    $halfStar = ($medecin['note_moyenne'] - $fullStars) >= 0.5;
                                                    $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
                                                    
                                                    for($i = 0; $i < $fullStars; $i++) {
                                                        echo '<i class="fas fa-star"></i>';
                                                    }
                                                    if($halfStar) {
                                                        echo '<i class="fas fa-star-half-alt"></i>';
                                                    }
                                                    for($i = 0; $i < $emptyStars; $i++) {
                                                        echo '<i class="far fa-star"></i>';
                                                    }
                                                    ?>
                                                    <span class="ms-1">(<?= $medecin['nombre_avis'] ?> avis)</span>
                                                </div>
                                            <?php else: ?>
                                                <div class="no-rating">Pas encore noté</div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <a href="profile_d.php?id=<?= $medecin['id_utilisateur'] ?>" class="btn btn-outline-primary btn-sm">
                                                Voir le profil
                                            </a>
                                            <a href="appointment.php?doctor_id=<?= $medecin['id_utilisateur'] ?>" class="btn btn-primary btn-sm">
                                                Prendre RDV
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning">
                        Aucun médecin ne correspond à votre recherche. Essayez d'autres termes.
                    </div>
                    <div class="text-center mt-4">
                        <a href="home.php" class="btn btn-primary">Nouvelle recherche</a>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Filtres optionnels -->
            <div class="filters mt-5">
                <h4>Affiner votre recherche</h4>
                <form method="post" class="row g-3">
                    <div class="col-md-4">
                        <label for="cherche" class="form-label">Nom, prénom ou adresse</label>
                        <input type="text" name="cherche" id="cherche" class="form-control" 
                               value="<?= htmlspecialchars($searchTerm) ?>" placeholder="Rechercher...">
                    </div>
                    
                    <div class="col-md-4">
                        <label for="specialite" class="form-label">Spécialité</label>
                        <select name="specialite" id="specialite" class="form-select">
                            <option value="">Toutes les spécialités</option>
                            <?php foreach($specialites as $spec): ?>
                                <option value="<?= htmlspecialchars($spec) ?>" <?= ($specialite === $spec) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($spec) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="ville" class="form-label">Ville</label>
                        <input type="text" name="ville" id="ville" class="form-control" 
                               value="<?= htmlspecialchars($ville) ?>" placeholder="Filtrer par ville">
                    </div>
                    
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Appliquer les filtres</button>
                        <a href="search.php" class="btn btn-outline-secondary ms-2">Réinitialiser</a>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <img src="images/Untitled-1.png" alt="CuraMed" class="footer-logo">
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
    // Gestion du tri
    document.querySelectorAll('.sort-option').forEach(option => {
        option.addEventListener('click', function() {
            const sort = this.dataset.sort;
            const url = new URL(window.location.href);
            url.searchParams.set('sort', sort);
            window.location.href = url.toString();
        });
    });
    </script>
</body>
</html>
<?php
// Fermer la connexion
mysqli_close($conn);
?>