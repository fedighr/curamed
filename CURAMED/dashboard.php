<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "curamed");
if (!$conn) {
    die("Database connection error");
}
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="scripts/dashboard.js"></script>

</head>
<body>

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
                
                <div class="profile-dropdown" id="profileDropdown">
                    <img src="<?php echo ($_SESSION['photo']); ?>" class="profile-image" alt="">
                    <div class="dropdown-menu">
                        <a href="profile.php" class="dropdown-item">
                            <i class="fas fa-user"></i> Mon profil
                        </a>
                        <a href="settings.html" class="dropdown-item">
                            <i class="fas fa-cog"></i> Paramètres
                        </a>
                        
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

    <!-- Dashboard Main Content -->
    <main class="dashboard-main container">
    <div class="container mt-3">
        <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= $_SESSION['error']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); endif; ?>
    </div>
        <div class="quick-stats-grid">
            <div class="stat-card primary">
                <i class="fas fa-user-injured"></i>
                <div class="stat-content">
                    <h3>Patients</h3>
                    <div class="stat-number"><?php
                            $req = "SELECT count(*) as num_p FROM utilisateur WHERE type_utilisateur='patient'";
                            if($res=mysqli_query($conn,$req)){
                                $row = mysqli_fetch_assoc($res);
                                echo($row['num_p']);
                            }    
                    ?></div>
                    <span class="stat-trend positive">↑ 12%</span>
                </div>
            </div>
            
            <div class="stat-card success">
                <i class="fas fa-user-md"></i>
                <div class="stat-content">
                    <h3>Médecins</h3>
                    <div class="stat-number"><?php
                            $req = "SELECT count(*) as num_m FROM utilisateur WHERE type_utilisateur='medecin'";
                            if($res=mysqli_query($conn,$req)){
                                $row = mysqli_fetch_assoc($res);
                                echo($row['num_m']);
                            }    
                    ?></div>
                    <span class="stat-trend positive">↑ 5%</span>
                </div>
            </div>
            
            <div class="stat-card warning">
                <i class="fas fa-calendar-check"></i>
                <div class="stat-content">
                    <h3>Rendez-vous</h3>
                    <div class="stat-number"><?php
                            $req = "SELECT count(*) as num_r FROM rendez_vous";
                            if($res=mysqli_query($conn,$req)){
                                $row = mysqli_fetch_assoc($res);
                                echo($row['num_r']);
                            }    
                    ?></div>
                    <span class="stat-trend negative">↓ 3%</span>
                </div>
            </div>
        </div>


        <div class="dashboard-tabs">
            <button class="tab-btn active" data-target="patients">
                <i class="fas fa-procedures"></i>
                Patients
            </button>
            <button class="tab-btn" data-target="doctors">
                <i class="fas fa-user-md"></i>
                Médecins
            </button>
        </div>

    <form id="patientForm" enctype="multipart/form-data" action="controll_dashboard.php" method='POST'>
        <div class="dashboard-content">
            <section id="patients" class="content-section active">
            <div class="container mt-3">
                <?php if(isset($_SESSION['message'])): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= $_SESSION['message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['message']); endif; ?>
            </div>
            <div class="container mt-3">
                <?php if(isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= $_SESSION['error']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['error']); endif; ?>

                <?php if(isset($_SESSION['message'])): ?>
                <!-- Existing success message code -->
                <?php endif; ?>
            </div>
                <div class="section-header">
                    <h2><i class="fas fa-procedures"></i> Gestion des Patients</h2>
                    <button type="button" class="btn btn-primary" data-target-modal="userModal" data-user-type="patient">
                        <i class="fas fa-plus"></i> Nouveau Patient
                    </button>
                </div>
                
                <div class="animated-table-container">
                    <?php $sql = "SELECT * FROM utilisateur where type_utilisateur='patient';";
                        $result = $conn->query($sql);
                    ?>
                    <table class="hover-table">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Rôle</th>
                            <th>Contact</th>
                            <th>Dernière Visite</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                        <tbody>
                            <?php while($user = $result->fetch_assoc()) { ?>
                            <tr>
                                <td>
                                    <div class="user-info">
                                        <img src="<?php echo $user['photo_profil']; ?>" class="user-avatar">
                                        <div>
                                        <div class="user-name"><?php echo $user['prenom']." ".$user['nom']; ?></div>
                                        <div class="user-id" name="id_user" value="<?php echo $user['id_utilisateur']; ?>">ID: <?php echo $user['id_utilisateur']; ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="role-info">
                                        <span class="role-badge role-<?= strtolower($user['role']) ?>">
                                            <?= ucfirst($user['role']) ?>
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div class="contact-info">
                                        <div><i class="fas fa-envelope"></i> <?php echo $user['email'];  ?></div>
                                        <div><i class="fas fa-phone"></i><?php echo $user['telephone'];  ?></div>
                                    </div>
                                </td>
                                <td><?php echo $user['date_connexion'];  ?></td>
                                <td><span class="status-badge success">Actif</span></td>
                                <td>
                                    <div class="action-buttons">
                                        <form method="POST" action="controll_dashboard.php">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id_utilisateur']; ?>">
                                            <input type="hidden" name="user_type" value="patient">
                                            <button type="submit" class="icon-btn" title="Voir le profil" name="profile">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="icon-btn edit-btn" title="Modifier" 
                                                    data-target-modal="editUserModal" 
                                                    data-user-id="<?php echo $user['id_utilisateur']; ?>"
                                                    data-user-type="patient">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <?php if($user['role'] === 'regular') : ?>
                                                <button type="submit" class="icon-btn" title="Rendre admin" name="admin">
                                                    <i class="fas fa-user-cog"></i>
                                                </button>
                                            <?php else : ?>
                                                <button type="submit" class="icon-btn" title="Supprimer admin" name="nadmin">
                                                    <i class="fas fa-user-slash"></i>
                                                </button>
                                            <?php endif; ?>
                                            <button type="submit" class="icon-btn delete-btn" title="supprimer" name="supp">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php } ?>
                    </table>
                </div>
            </section>
    </form>

    <form id="doctorForm" enctype="multipart/form-data" action ="controll_dashboard.php" method="POST">
            <section id="doctors" class="content-section">
                    <div class="container mt-3">
            <?php if(isset($_SESSION['message'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= $_SESSION['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
                <?php unset($_SESSION['message']); endif; ?>
            </div>
            <div class="container mt-3">
        <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= $_SESSION['error']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); endif; ?>

        <?php if(isset($_SESSION['message'])): ?>
        <!-- Existing success message code -->
        <?php endif; ?>
    </div>
    <div class="section-header">
        <h2><i class="fas fa-user-md"></i> Gestion des Médecins</h2>
        <button type="button" class="btn btn-primary" data-target-modal="userModal" data-user-type="medecin">
            <i class="fas fa-plus"></i> Nouveau Médecin
        </button>
    </div>
    
    <div class="animated-table-container">
        <?php 
        $sql = "SELECT * FROM utilisateur WHERE type_utilisateur = 'medecin'";
        $result = $conn->query($sql);
        ?>
        <table class="hover-table">
        <thead>
                <tr>
                    <th>Médecin</th>
                    <th>Rôle</th>
                    <th>Spécialité</th>
                    <th>Contact</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($doctor = $result->fetch_assoc()) { ?>
                <tr>
                    <td>
                        <div class="user-info">
                            <img src="<?php echo $doctor['photo_profil']; ?>" class="user-avatar">
                            <div>
                                <div class="user-name">Dr. <?php echo $doctor['prenom']." ".$doctor['nom']; ?></div>
                                <div class="user-id">ID: <?php echo $doctor['id_utilisateur']; ?></div>
                            </div>
                        </div>
                    </td>
                    <td>
                    <div class="role-info">
                        <span class="role-badge role-<?= strtolower($doctor['role']) ?>">
                            <?= ucfirst($doctor['role']) ?>
                        </span>
                    </div>
                </td>
                    <td><?php 

                        $req = "SELECT * FROM medecin WHERE id_medecin = " . $doctor['id_utilisateur'];
                        $res = mysqli_query($conn, $req);

                        if ($res) {
                            $row = mysqli_fetch_assoc($res);
                            echo $row['specialite'];
                        } else {
                            echo "Error: " . mysqli_error($conn);
                        }
                    
                    ?></td>
                    <td>
                        <div class="contact-info">
                            <div><i class="fas fa-envelope"></i> <?php echo $doctor['email']; ?></div>
                            <div><i class="fas fa-phone"></i> <?php echo $doctor['telephone']; ?></div>
                        </div>
                    </td>
                    <td><span class="status-badge success">Actif</span></td>
                    <td>
                        <div class="action-buttons">
                            <form id="modalUserForm " method="POST" action="controll_dashboard.php" enctype="multipart/form-data">
                                <input type="hidden" name="user_id" value="<?php echo $doctor['id_utilisateur']; ?>">
                                <input type="hidden" name="user_type" value="medecin">
                                <button type="submit" class="icon-btn" title="Voir le profil" name="profile">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" class="icon-btn edit-btn" title="Modifier" 
                                        data-target-modal="editUserModal" 
                                        data-user-id="<?php echo $doctor['id_utilisateur']; ?>"
                                        data-user-type="medecin">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <?php if($doctor['role'] === 'regular') : ?>
                                    <button type="submit" name="admin" class="icon-btn">
                                        <i class="fas fa-user-cog"></i>
                                    </button>
                                <?php else : ?>
                                    <button type="submit" class="icon-btn" title="Supprimer admin" name="nadmin">
                                        <i class="fas fa-user-slash"></i>
                                    </button>
                                <?php endif; ?>
                                <button type="submit" name="supp" class="icon-btn delete-btn">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</section>
</form>

        <aside class="recent-activity">
            <h3><i class="fas fa-bell"></i> Activité Récente</h3>
            <div class="activity-list">
                <div class="activity-item new">
                    <div class="activity-icon">
                        <i class="fas fa-calendar-plus"></i>
                    </div>
                    <div class="activity-content">
                        <div class="activity-title">Nouveau rendez-vous</div>
                        <div class="activity-time">Il y a 2 heures</div>
                    </div>
                </div>
                
                <div class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-file-medical"></i>
                    </div>
                    <div class="activity-content">
                        <div class="activity-title">Ordonnance mise à jour</div>
                        <div class="activity-time">Aujourd'hui 10:30</div>
                    </div>
                </div>
            </div>
        </aside>
    </main>

    <div class="modal fade" id="userModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="userModalLabel">Nouvel Utilisateur</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="userForm" enctype="multipart/form-data" action="controll_dashboard.php" method="POST">
        <div class="modal-body">
          <div class="row g-3">
            <input type="hidden" name="user_type" id="modalUserType">
            <div class="col-md-6">
              <label class="form-label">Nom</label>
              <input type="text" name="nom" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Prénom</label>
              <input type="text" name="prenom" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Téléphone</label>
              <input type="tel" name="telephone" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Mot de passe</label>
              <input type="password" name="password" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Confirmation</label>
              <input type="password" name="confirm_password" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Photo de profil</label>
              <input type="file" name="photo" class="form-control">
            </div>
            <div class="col-md-6 specialite-field" style="display: none;">
              <label class="form-label">Spécialité</label>
              <input type="text" name="specialite" class="form-control">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-primary">Enregistrer</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Modifier l'utilisateur</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="editUserForm" enctype="multipart/form-data" action="controll_dashboard.php" method="POST">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="user_id" id="editUserId">
        <input type="hidden" name="user_type" id="editUserType">
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Nom</label>
              <input type="text" name="nom" id="editNom" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Prénom</label>
              <input type="text" name="prenom" id="editPrenom" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Email</label>
              <input type="email" name="email" id="editEmail" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Téléphone</label>
              <input type="tel" name="telephone" id="editTelephone" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Nouveau mot de passe (laisser vide si inchangé)</label>
              <input type="password" name="password" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Confirmation</label>
              <input type="password" name="confirm_password" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Photo de profil</label>
              <input type="file" name="photo" class="form-control">
            </div>
            <div class="col-md-6 specialite-edit-field" style="display: none;">
              <label class="form-label">Spécialité</label>
              <input type="text" name="specialite" id="editSpecialite" class="form-control">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-primary">Enregistrer</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php mysqli_close($conn); ?>
</body>
</html>