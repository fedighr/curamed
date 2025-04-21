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
";
$res = mysqli_query($conn, $sql);
if (!$res) {
    die('Query failed: ' . mysqli_error($conn));
}
$doctors = mysqli_fetch_all($res, MYSQLI_ASSOC);
mysqli_free_result($res);


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
    'doctors'   => $doctors
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

<div class="container dashboard-main">
    <!-- Filters Sidebar -->
    <aside class="sidebar">
      <div class="filter-card">
        <h3 class="section-subtitle">Filtrer les résultats</h3>
        
        <div class="filter-group">
          <input type="text" class="search-input" placeholder="Nom du médecin..." value="<?= htmlspecialchars($_POST['search'] ?? '') ?>">
        </div>

        <div class="filter-group">
          <h4 class="filter-title">Spécialité</h4>
          <select class="search-select">
            <option value="">Toutes les spécialités</option>
            <!-- Add PHP loop for specialties -->
          </select>
        </div>

        <div class="filter-group">
          <h4 class="filter-title">Localisation</h4>
          <div class="location-filters">
            <select class="search-select">
              <option value="">Toute la Tunisie</option>
              <!-- Add PHP loop for locations -->
            </select>
          </div>
        </div>

        <div class="filter-group">
          <h4 class="filter-title">Options</h4>
          <div class="filter-option">
            <input type="checkbox" id="cnam">
            <label for="cnam">Conventionné CNAM</label>
          </div>
          <div class="filter-option">
            <input type="checkbox" id="domicile">
            <label for="domicile">Visite à domicile</label>
          </div>
        </div>
      </div>
    </aside>

    <!-- Main Results -->
    <main class="main-content">
      <h1 class="section-title">Résultats de recherche</h1>
      <p class="section-subtitle"><?= count($results) ?> médecins trouvés</p>

      <?php if ($results): ?>
        <div class="results-grid">
          <?php foreach ($results as $doc): ?>
          <div class="doctor-card animate-slideup">
            <div class="doctor-header">
              <div class="doctor-avatar-container">
                <?php if ($doc['photo']): ?>
                <img src="<?= htmlspecialchars($doc['photo']) ?>" class="doctor-avatar" alt="Photo du médecin">
                <?php endif; ?>
                <div class="relevance-badge"><?= round($doc['score'] * 100) ?>%</div>
              </div>
              
              <div class="doctor-meta">
                <h2 class="doctor-name">Dr. <?= htmlspecialchars($doc['name']) ?></h2>
                <div class="doctor-specialty"><?= htmlspecialchars($doc['specialty']) ?></div>
                <div class="doctor-location">
                  <i class="fas fa-map-marker-alt"></i>
                  <?= htmlspecialchars($doc['address']) ?>
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
                <a href="#" class="btn btn-primary">Voir le profil</a>
                <a href="#" class="btn btn-outline-primary">Prendre RDV</a>
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
</body>
</html>
