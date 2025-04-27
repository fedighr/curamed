<?php
session_start();
$conn=mysqli_connect("localhost","root","","curamed");

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
                                        class="i+-con-btn fas fa-check text-success" 
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


    <section class="hero">
        <form id="search" action="search.php" method="POST" enctype="multipart/form-data">
            <div class="container">
                <div class="hero-content">
                    <div class="hero-text">
                        <h1 class="hero-title">Prenez rendez-vous avec un médecin en ligne</h1>
                        <p class="hero-subtitle">Trouvez le spécialiste qu'il vous faut et consultez rapidement</p>
                        
                        <div class="search-container">
                        <div class="search-input-group">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" class="search-input" placeholder="Spécialité , médecin" name="cherche" id="search">
                        </div>


                        <div class="search-select-group">
                        <select id="specialite" name="specialite" class="search-select">
                            <option value="" disabled selected hidden>Spécialité</option>
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

                        <button class="search-button btn btn-primary">
                            Rechercher
                        </button>
                    </div>
                  
                        <div class="popular-searches">
                            <span>Recherches populaires :</span>
                            <a href="#">Médecin généraliste</a>
                            <a href="#">Dermatologue</a>
                            <a href="#">Pédiatre</a>
                        </div>
                        
                    </div>
                    <div class="hero-image">
                        <img src="images\im2.png" alt="Docteur en consultation" class="img-fluid">
                    </div>
                </div>
            </div>
        </form>
        <li>Esseyer notre nouveau modéle Ai pour assurer votre chemin vers le bon médecin :  </li>
        <button class="btn3" onclick="window.location.href='test.php'">Click ici</button>
        
    </section>


    <section class="stats-section">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number">80M+</div>
                    <div class="stat-label">patients satisfaits</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">450k</div>
                    <div class="stat-label">praticiens qualifiés</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">24/7</div>
                    <div class="stat-label">disponibilité</div>
                </div>
            </div>
        </div>
    </section>


    <section class="steps-section">
        <div class="container">
            <h2 class="section-title">Comment ça marche</h2>
            <p class="section-subtitle">Prendre rendez-vous n'a jamais été aussi simple</p>
            
            <div class="steps-container">
                <div class="step-card">
                    <div class="step-icon">
                        <i class="fas fa-search"></i>
                        <div class="step-number">1</div>
                    </div>
                    <h3>Trouvez</h3>
                    <p>Recherchez par spécialité ou symptôme</p>
                </div>
                <div class="step-card">
                    <div class="step-icon">
                        <i class="fas fa-calendar-check"></i>
                        <div class="step-number">2</div>
                    </div>
                    <h3>Choisissez</h3>
                    <p>Sélectionnez votre créneau idéal</p>
                </div>
                <div class="step-card">
                    <div class="step-icon">
                        <i class="fas fa-user-md"></i>
                        <div class="step-number">3</div>
                    </div>
                    <h3>Consultez</h3>
                    <p>En cabinet ou en vidéo</p>
                </div>
            </div>
        </div>
    </section>

    <section class="doctors1-carousel-section">
  <div class="doctors1-carousel-container">
    <h2 class="carousel-title">Rencontrez nos spécialistes</h2>
    <p class="carousel-subtitle">Des professionnels de santé qualifiés près de chez vous</p>
    
    <div class="carousel-container">
      <button class="carousel-btn prev-btn">&#10094;</button>
      
      <div class="carousel-track">
        <?php

        
        try {

            $sql = "SELECT u.*, m.specialite, m.adresse_cabinet 
                    FROM utilisateur u
                    JOIN medecin m ON u.id_utilisateur = m.id_medecin
                    WHERE u.type_utilisateur = 'medecin' limit 8";
            
            $res=mysqli_query($conn,$sql);
          

               
        ?>
                 <?php 
                 while(  $doctor=mysqli_fetch_assoc($res)){ ?>
                <div class="doctor1-card">
                    <img src="<?= htmlspecialchars($doctor['photo_profil']) ?>" 
                         alt="Dr <?= htmlspecialchars($doctor['nom']) ?>" 
                         class="doctor1-img">
                    <h3 class="doctor1-name">Dr. <?= htmlspecialchars($doctor['prenom'] . ' ' . htmlspecialchars($doctor['nom'])) ?></h3>
                    <p class="specialty"><?= htmlspecialchars($doctor['specialite']) ?></p>
                    <p class="location"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($doctor['adresse_cabinet']) ?></p>
                    <button class="choose-btn" onclick="location.href='profile.php?id=<?= $doctor['id_utilisateur'] ?>'">Choisir</button>
                </div>
                <?php }?>
        <?php
            }
         catch (PDOException $e) {
            echo "Erreur de base de données : " . $e->getMessage();
         }
        ?>
      </div>
      
      <button class="carousel-btn next-btn">&#10095;</button>
    </div>
  </div>
</section>


    <section class="features-section">
        <div class="container">
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-video"></i>
                    </div>
                    <h3>Consultation vidéo</h3>
                    <p>Consultez depuis chez vous avec nos médecins certifiés</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3>Rendez-vous rapides</h3>
                    <p>Obtenez un créneau en moins de 24h dans 90% des cas</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-file-medical"></i>
                    </div>
                    <h3>Dossier médical</h3>
                    <p>Vos documents médicaux accessibles en un clic</p>
                </div>
            </div>
        </div>
    </section>


    <section class="testimonials-section">
        <div class="container">
            <h2 class="section-title">Ce que disent nos patients</h2>
            
            <div class="testimonials-carousel">
                <div class="testimonial-card">
                    <div class="testimonial-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="testimonial-text">
                        "J'ai pu obtenir un rendez-vous avec un dermatologue en moins de 2 heures pour un problème urgent. Excellent service !"
                    </p>
                    <div class="testimonial-author">
                        <img src="images/bakhana.jpeg" alt="Bakha D." class="author-avatar">
                        <div class="author-info">
                            <strong>Bakha D.</strong>
                            <span>Siliene</span>
                        </div>
                    </div>
                </div>
                

            </div>
        </div>
    </section>


    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>Prêt à consulter ?</h2>
                <p>Trouvez le médecin qu'il vous faut et prenez rendez-vous en quelques clics</p>
             
                <button class="btn btn-primary btn-lg" onclick="window.location.href='search.php'">Trouver un médecin</button>
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