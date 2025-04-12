<?php
session_start();
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
    <script src="./scripts/home.js"></script>

</head>
<body>
    <!-- Header -->
    <header class="header">
        <nav class="nav-container container">
            <a href="index.php" class="logo-link">
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
                
                <div class="profile-dropdown">
                    <img src="<?php echo ($_SESSION['photo']); ?>" class="profile-image" alt="">
                    <div class="dropdown-menu">
                        <a href="profile.html" class="dropdown-item">
                            <i class="fas fa-user"></i> Mon profil
                        </a>
                        <a href="settings.html" class="dropdown-item">
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
            </div>
            <button class="mobile-menu-btn d-lg-none">
                <i class="fas fa-bars"></i>
            </button>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <h1 class="hero-title">Prenez rendez-vous avec un médecin en ligne</h1>
                    <p class="hero-subtitle">Trouvez le spécialiste qu'il vous faut et consultez rapidement</p>
                    
                    <div class="search-container">
                        <div class="search-input-group">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" class="search-input" placeholder="Spécialité, symptôme, médecin...">
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
    </section>

    <!-- Stats Section -->
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

    <!-- How It Works Section -->
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

    <section class="doctors-carousel-section">
      <div class="doctors-carousel-container">
        <h2 class="carousel-title">Rencontrez nos spécialistes</h2>
        <p class="carousel-subtitle">Des professionnels de santé qualifiés près de chez vous</p>
        
        <div class="carousel-container">
          <button class="carousel-btn prev-btn">&#10094;</button>
          
          <div class="carousel-track">
            <!-- Doctor Card 1 -->
            <div class="doctor-card">
              <img src="images/doctor1.jpg" alt="Dr Sarah Dupont" class="doctor-img">
              <h3 class="doctor-name">Dr. Sarah Dupont</h3>
              <p class="specialty">Cardiologue</p>
              <p class="reviews">⭐ 4.8 (120 avis)</p>
              <p class="location"><i class="fas fa-map-marker-alt"></i> Paris</p>
              <button class="choose-btn">Choisir</button>
            </div>
            
            <!-- Doctor Card 2 -->
            <div class="doctor-card">
              <img src="images/doctor2.jpg" alt="Dr Jean Martin" class="doctor-img">
              <h3 class="doctor-name">Dr. Jean Martin</h3>
              <p class="specialty">Dermatologue</p>
              <p class="reviews">⭐ 4.5 (89 avis)</p>
              <p class="location"><i class="fas fa-map-marker-alt"></i> Lyon</p>
              <button class="choose-btn">Choisir</button>
            </div>
            
            <!-- Doctor Card 3 -->
            <div class="doctor-card">
              <img src="images/doctor3.jpg" alt="Dr Clara Lefevre" class="doctor-img">
              <h3 class="doctor-name">Dr. Clara Lefevre</h3>
              <p class="specialty">Pédiatre</p>
              <p class="reviews">⭐ 4.9 (200 avis)</p>
              <p class="location"><i class="fas fa-map-marker-alt"></i> Marseille</p>
              <button class="choose-btn">Choisir</button>
            </div>
            
            <!-- Doctor Card 4 -->
            <div class="doctor-card">
              <img src="images/doctor4.jpg" alt="Dr Lucas Morel" class="doctor-img">
              <h3 class="doctor-name">Dr. Lucas Morel</h3>
              <p class="specialty">Généraliste</p>
              <p class="reviews">⭐ 4.7 (150 avis)</p>
              <p class="location"><i class="fas fa-map-marker-alt"></i> Toulouse</p>
              <button class="choose-btn">Choisir</button>
            </div>
            
            <!-- Doctor Card 5 -->
            <div class="doctor-card">
              <img src="images/doctor5.jpg" alt="Dr Aline Petit" class="doctor-img">
              <h3 class="doctor-name">Dr. Aline Petit</h3>
              <p class="specialty">Ophtalmologue</p>
              <p class="reviews">⭐ 4.6 (98 avis)</p>
              <p class="location"><i class="fas fa-map-marker-alt"></i> Nice</p>
              <button class="choose-btn">Choisir</button>
            </div>
            
            <!-- Doctor Card 6 -->
            <div class="doctor-card">
              <img src="images/doctor6.jpg" alt="Dr David Chevalier" class="doctor-img">
              <h3 class="doctor-name">Dr. David Chevalier</h3>
              <p class="specialty">ORL</p>
              <p class="reviews">⭐ 4.4 (110 avis)</p>
              <p class="location"><i class="fas fa-map-marker-alt"></i> Bordeaux</p>
              <button class="choose-btn">Choisir</button>
            </div>
          </div>
          
          <button class="carousel-btn next-btn">&#10095;</button>
        </div>
      </div>
    </section>

    <!-- Features Section -->
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

    <!-- Testimonials -->
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
                        <img src="images/patient1.jpg" alt="Marie D." class="author-avatar">
                        <div class="author-info">
                            <strong>Marie D.</strong>
                            <span>Paris</span>
                        </div>
                    </div>
                </div>
                
                <!-- More testimonials would go here -->
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>Prêt à consulter ?</h2>
                <p>Trouvez le médecin qu'il vous faut et prenez rendez-vous en quelques clics</p>
                <button class="btn btn-primary btn-lg">Trouver un médecin</button>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <img src="images\logo.png" alt="CuraMed" class="footer-logo">
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
                    © 2023 CuraMed. Tous droits réservés.
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php mysqli_close($conn); ?>
</body>
</html>