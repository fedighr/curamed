<?php
session_start();
header('Content-Type: text/html; charset=utf-8');

$conn = mysqli_connect('localhost', 'root', '', 'curamed');
if (!$conn) {
    die('Connection failed: ' . mysqli_connect_error());
}


$sql = "
    SELECT 
        u.id_utilisateur, u.nom, u.prenom, u.photo_profil,
        m.specialite, m.adresse_cabinet AS adresse, m.experience
    FROM utilisateur u
    JOIN medecin m ON u.id_utilisateur = m.id_medecin
    WHERE 1=1
";

if (!empty($_POST['specialite'])) {
  $specialty = mysqli_real_escape_string($conn, $_POST['specialite']);
  $sql .= " AND m.specialite = '$specialty'";
}

if(!empty($_POST["ville"])){
  $ville = mysqli_real_escape_string($conn, $_POST['ville']);
  $sql .= " AND m.ville = '$ville'";
}

if(!empty($_POST["genre"])){
  $genre = mysqli_real_escape_string($conn, $_POST['genre']);
  $sql .= " AND u.genre = '$genre'";
}
$res = mysqli_query($conn, $sql);
if (!$res) {
    die('Query failed: ' . mysqli_error($conn));
}
$results = mysqli_fetch_all($res, MYSQLI_ASSOC);
mysqli_free_result($res);

if(!empty($_POST['cherche'])){
$tempDir = __DIR__ . '/temp';
if (!is_dir($tempDir)) {
    mkdir($tempDir, 0755, true);
}
$sessionId  = session_id();
$inputFile  = "$tempDir/{$sessionId}_input.json";
$outputFile = "$tempDir/{$sessionId}_output.json";


$inputData = [
    'query'     => $_POST['cherche'] ?? '',
    'specialty' => $_POST['specialite'] ?? '',
    'doctors'   => $results
];
file_put_contents($inputFile, json_encode($inputData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));


$pythonPath = 'C:\\Program Files\\python.exe';
$scriptPath = __DIR__ . '\\indexation\\found_med.py';

$cmd = '"' . $pythonPath . '" "' . $scriptPath . '" "' . $inputFile . '" "' . $outputFile . '" 2>&1';
exec($cmd, $output, $returnCode);


if ($returnCode !== 0 || !file_exists($outputFile)) {
    echo "<h2>❌ Erreur lors de l'exécution du script Python</h2>";
    echo "<p><strong>Commande:</strong> $cmd</p>";
    echo "<p><strong>Code de retour:</strong> $returnCode</p>";
    echo "<pre><strong>Sortie Python:</strong>\n" . htmlspecialchars(implode("\n", $output)) . "</pre>";
    echo "<p><strong>Contenu de l'input JSON:</strong></p><pre>" . htmlspecialchars(file_get_contents($inputFile)) . "</pre>";
    exit;
}


$results = json_decode(file_get_contents($outputFile), true) ?: [];
unlink($inputFile);
unlink($outputFile);
mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CuraMed - Search</title>
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
<body class="bg-light">

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

    <div class="container dashboard-main" id="filtre">

  <aside class="search-sidebar">
  <form id="filter_form" method="POST">
  <div class="filter-card">

  <div class="filter-group">
    <div class="search-input-group">
      <i class="fas fa-search search-icon"></i>
      <input type="text" class="search-input" placeholder="Nom du professionnel" name="cherche">
      <button class="btn btn-primary">OK</button>
    </div>
  </div>


  <div class="filter-group">
  <label for="specialty-input">Spécialité</label>
  <select name="specialite" id="specialty-input" class="form-select">
  <option value="" disabled selected hidden>Specialité</option>
    <option value="Cardiologie">Cardiologie</option>
    <option value="Dermatologie">Dermatologie</option>
    <option value="Pédiatrie">Psychiatrie</option>
    <option value="Gynécologie">Orthopédie</option>
    <option value="Médecine Générale">Médecine Générale</option>
    <option value="Gastro-entérologie">Gastro-entérologie</option>
    <option value="Neurologie">Neurologie</option>
    <option value="Pneumologie">Pneumologie</option>
    <option value="Rhumatologie">Rhumatologie</option>
    <option value="Ophtalmologie">Ophtalmologie</option>
  </select>
</div>

<div class="filter-group">
  <label for="ville-input">Ville</label>
  <select name="ville" id="ville-input" class="form-select">
  <option value="" disabled selected hidden>Ville</option>
    <option value="Tunis">Tunis</option>
    <option value="Ariana">Ariana</option>
    <option value="Ben Arous">Ben Arous</option>
    <option value="Manouba">Manouba</option>
    <option value="Nabeul">Nabeul</option>
    <option value="Zaghouan">Zaghouan</option>
    <option value="Bizerte">Bizerte</option>
    <option value="Béja">Béja</option>
    <option value="Jendouba">Jendouba</option>
    <option value="Le Kef">Le Kef</option>
    <option value="Siliana">Siliana</option>
    <option value="Kairouan">Kairouan</option>
    <option value="Kasserine">Kasserine</option>
    <option value="Sidi Bouzid">Sidi Bouzid</option>
    <option value="Sousse">Sousse</option>
    <option value="Monastir">Monastir</option>
    <option value="Mahdia">Mahdia</option>
    <option value="Sfax">Sfax</option>
    <option value="Gafsa">Gafsa</option>
    <option value="Tozeur">Tozeur</option>
    <option value="Kebili">Kebili</option>
    <option value="Gabès">Gabès</option>
    <option value="Médenine">Médenine</option>
    <option value="Tataouine">Tataouine</option>
  </select>
</div>

<div class="filter-group">
  <label for="genre-input">Genre</label>
  <select name="genre" id="genre-input" class="form-select">
  <option value="" disabled selected hidden>Genre</option>
    <option value="Homme">Homme</option>
    <option value="Femme">Femme</option>
  </select>
</div>


  <button type="submit" class="btn btn-primary filter-submit-btn" id="applyFilters">
    Appliquer les filtres
  </button>
</div>
</form>
</aside>


    <main class="main-content">
      <h1 class="section-title">Résultats de recherche</h1>
      <p class="section-subtitle"><?= count($results) ?> médecins trouvés</p>

      <?php if ($results): ?>
        <div class="results-grid">
          <?php foreach ($results as $doc): ?>
          <div class="doctor-card animate-slideup">
            <div class="doctor-header">
              <div class="doctor-avatar-container">
                <?php if ($doc['photo_profil']): ?>
                <img src="<?= htmlspecialchars($doc['photo_profil']) ?>" class="doctor-avatar" alt="Photo du médecin">
                <?php endif; ?>
                <?php if(isset($doc['score'])) { ?>
                <div class="relevance-badge"><?= round($doc['score'] * 100) ?>%</div><?php } ?>
              </div>
              
              <div class="doctor-meta">
                <h2 class="doctor-name">Dr. <?= htmlspecialchars($doc['nom']) ?></h2>
                <div class="doctor-specialty"><?= htmlspecialchars($doc['specialite']) ?></div>
                <div class="doctor-location">
                  <i class="fas fa-map-marker-alt"></i>
                  <?= htmlspecialchars($doc['adresse']) ?>
                </div>
              </div>
            </div>

            <div class="doctor-details">
              <div class="detail-item">
                <div class="detail-label">Expérience</div>
                <div class="detail-value"><?= htmlspecialchars($doc['experience']) ?> ans</div>
              </div>
              
              <div class="detail-item">
                <div class="detail-label">Disponibilité</div>
                <div class="availability-status available">● Disponible maintenant</div>
              </div>

              <div class="action-buttons">
              <a href="profile.php?id=<?= $doc['id_utilisateur'] ?>" class="btn btn-outline-primary">Prendre RDV</a>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="empty-state">
          <i class="fas fa-stethoscope"></i>
          <h3>Aucun médecin trouvé</h3>
          <p>Essayez de modifier vos critères de recherche</p>
        </div>
      <?php endif; ?>
    </main>
  </div>
  <script>
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.doctor-card').forEach((card, i) => {
    setTimeout(() => card.classList.add('animate-slideup'), i * 100);
  });
});
</script>
</style>
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
</body>
</html>
