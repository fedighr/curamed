-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 26, 2025 at 05:22 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `curamed`
--

-- --------------------------------------------------------

--
-- Table structure for table `calendrier_medecin`
--

CREATE TABLE `calendrier_medecin` (
  `id_calendrier` int(11) NOT NULL,
  `id_medecin` int(11) NOT NULL,
  `jour` varchar(20) NOT NULL,
  `heure_debut` time NOT NULL,
  `heure_fin` time NOT NULL,
  `pause_debut` time NOT NULL,
  `pause_fin` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `calendrier_medecin`
--

INSERT INTO `calendrier_medecin` (`id_calendrier`, `id_medecin`, `jour`, `heure_debut`, `heure_fin`, `pause_debut`, `pause_fin`) VALUES
(66, 73, 'Lundi', '08:00:00', '16:30:00', '09:30:00', '12:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `diagnostic`
--

CREATE TABLE `diagnostic` (
  `id_diagnostic` int(11) NOT NULL,
  `id_rdv` int(11) NOT NULL,
  `id_medecin` int(11) NOT NULL,
  `nom_maladie` varchar(100) NOT NULL,
  `date_diagnostic` date NOT NULL,
  `taux_confiance` double NOT NULL,
  `source` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `facture`
--

CREATE TABLE `facture` (
  `id_facture` int(11) NOT NULL,
  `id_paiement` int(11) NOT NULL,
  `date_facture` date NOT NULL,
  `continue_facture` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fiche_medicale`
--

CREATE TABLE `fiche_medicale` (
  `id_fiche` int(11) NOT NULL,
  `id_rdv` int(11) NOT NULL,
  `fichier_pdf` text NOT NULL,
  `date_creation` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fiche_medicale`
--

INSERT INTO `fiche_medicale` (`id_fiche`, `id_rdv`, `fichier_pdf`, `date_creation`) VALUES
(3, 10, 'pdf/patient_83_20250426.pdf', '2025-04-26 17:15:55');

-- --------------------------------------------------------

--
-- Table structure for table `historique`
--

CREATE TABLE `historique` (
  `id_consultation` int(11) NOT NULL,
  `id_patient` int(11) NOT NULL,
  `id_medecin` int(11) NOT NULL,
  `id_rdv` int(11) NOT NULL,
  `date_consultation` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `historique`
--

INSERT INTO `historique` (`id_consultation`, `id_patient`, `id_medecin`, `id_rdv`, `date_consultation`) VALUES
(1, 83, 77, 10, '2025-04-26 17:15:55');

-- --------------------------------------------------------

--
-- Table structure for table `maladies`
--

CREATE TABLE `maladies` (
  `id_maladie` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `specialite` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `maladies`
--

INSERT INTO `maladies` (`id_maladie`, `nom`, `specialite`) VALUES
(1, 'Grippe', 'Médecine Générale'),
(2, 'Rhume', 'Médecine Générale'),
(3, 'Fatigue générale', 'Médecine Générale'),
(4, 'Hypertension', 'Cardiologie'),
(5, 'Insuffisance cardiaque', 'Cardiologie'),
(6, 'Angine de poitrine', 'Cardiologie'),
(7, 'Eczéma', 'Dermatologie'),
(8, 'Acné', 'Dermatologie'),
(9, 'Psoriasis', 'Dermatologie'),
(10, 'Gastrite', 'Gastro-entérologie'),
(11, 'Reflux gastro-œsophagien', 'Gastro-entérologie'),
(12, 'Ulcère gastrique', 'Gastro-entérologie'),
(13, 'Migraine', 'Neurologie'),
(14, 'Epilepsie', 'Neurologie'),
(15, 'Sclérose en plaques', 'Neurologie'),
(16, 'Asthme', 'Pneumologie'),
(17, 'Pneumonie', 'Pneumologie'),
(18, 'Bronchite', 'Pneumologie'),
(19, 'Arthrite', 'Rhumatologie'),
(20, 'Lupus', 'Rhumatologie'),
(21, 'Ostéoporose', 'Rhumatologie'),
(22, 'Conjonctivite', 'Ophtalmologie'),
(23, 'Cataracte', 'Ophtalmologie'),
(24, 'Glaucome', 'Ophtalmologie'),
(25, 'Scoliose', 'Orthopédie'),
(26, 'Arthrose', 'Orthopédie'),
(27, 'Scoliose', 'Orthopédie'),
(28, 'Dépression', 'Psychiatrie'),
(29, 'Anxiété', 'Psychiatrie'),
(30, 'Trouble bipolaire', 'Psychiatrie');

-- --------------------------------------------------------

--
-- Table structure for table `maladi_symtome`
--

CREATE TABLE `maladi_symtome` (
  `id_maladie` int(11) NOT NULL,
  `id_symptome` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `maladi_symtome`
--

INSERT INTO `maladi_symtome` (`id_maladie`, `id_symptome`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(2, 5),
(2, 6),
(2, 7),
(2, 8),
(3, 9),
(3, 10),
(3, 11),
(3, 12),
(4, 3),
(4, 13),
(4, 14),
(4, 15),
(5, 14),
(5, 16),
(5, 17),
(5, 18),
(6, 14),
(6, 19),
(6, 20),
(6, 21),
(7, 22),
(7, 23),
(7, 24),
(7, 25),
(8, 26),
(8, 27),
(8, 28),
(8, 29),
(9, 22),
(9, 25),
(9, 30),
(9, 31),
(10, 12),
(10, 21),
(10, 32),
(10, 33),
(11, 19),
(11, 80),
(11, 81),
(11, 82),
(12, 21),
(12, 32),
(12, 34),
(12, 35),
(13, 21),
(13, 36),
(13, 37),
(13, 38),
(14, 39),
(14, 40),
(14, 41),
(14, 42),
(15, 2),
(15, 43),
(15, 44),
(15, 45),
(16, 14),
(16, 46),
(16, 47),
(16, 48),
(17, 1),
(17, 14),
(17, 19),
(17, 49),
(18, 2),
(18, 14),
(18, 50),
(18, 51),
(19, 52),
(19, 53),
(19, 54),
(19, 55),
(20, 16),
(20, 52),
(20, 56),
(20, 57),
(21, 58),
(21, 59),
(21, 60),
(21, 61),
(22, 37),
(22, 62),
(22, 63),
(22, 64),
(23, 15),
(23, 37),
(23, 65),
(23, 66),
(24, 3),
(24, 15),
(24, 67),
(24, 68),
(25, 69),
(25, 70),
(25, 71),
(25, 72),
(26, 52),
(26, 53),
(26, 54),
(26, 73),
(27, 14),
(27, 74),
(27, 75),
(27, 76),
(28, 2),
(28, 77),
(28, 78),
(28, 79),
(29, 14),
(29, 18),
(29, 41),
(29, 80),
(30, 77),
(30, 81),
(30, 82);

-- --------------------------------------------------------

--
-- Table structure for table `medecin`
--

CREATE TABLE `medecin` (
  `id_medecin` int(11) NOT NULL,
  `specialite` varchar(20) NOT NULL,
  `adresse_cabinet` varchar(50) NOT NULL,
  `experience` text NOT NULL,
  `ville` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medecin`
--

INSERT INTO `medecin` (`id_medecin`, `specialite`, `adresse_cabinet`, `experience`, `ville`) VALUES
(68, 'Dermatologie', '', '', 'Monastir'),
(72, 'Dermatologie', 'zelba', 'sqdqsdqd', 'Mahdia'),
(73, 'sqdqsdqsd', '', '', 'Mahdia'),
(77, 'Chirurgie', 'zelba', '20', 'Mahdia'),
(82, 'Chirurgie', 'ksour essef', '', 'Jendouba');

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `id_notification` int(11) NOT NULL,
  `id_utilisateur` int(11) NOT NULL,
  `message` text NOT NULL,
  `date_envoi` date NOT NULL,
  `type` enum('email','sms') NOT NULL,
  `statut` enum('envoyé','non_lu','lu','échoué') NOT NULL,
  `id_patient` int(11) DEFAULT NULL,
  `id_rdv` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notification`
--

INSERT INTO `notification` (`id_notification`, `id_utilisateur`, `message`, `date_envoi`, `type`, `statut`, `id_patient`, `id_rdv`) VALUES
(13, 83, 'rendez vous accepter avec docteur ghribie fadi', '2025-04-26', 'email', 'envoyé', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `paiement`
--

CREATE TABLE `paiement` (
  `id_paiement` int(11) NOT NULL,
  `id_rdv` int(11) NOT NULL,
  `id_patient` int(11) NOT NULL,
  `montant` int(11) NOT NULL,
  `mode_paiement` enum('visa','cash','D17') NOT NULL,
  `date_paiement` date NOT NULL,
  `statut` enum('payé','echoué','en attente') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `paiement`
--

INSERT INTO `paiement` (`id_paiement`, `id_rdv`, `id_patient`, `montant`, `mode_paiement`, `date_paiement`, `statut`) VALUES
(7, 10, 83, 60, 'cash', '2025-04-25', 'payé'),
(8, 11, 83, 60, 'visa', '2025-04-26', 'en attente');

-- --------------------------------------------------------

--
-- Table structure for table `patient`
--

CREATE TABLE `patient` (
  `id_patient` int(11) NOT NULL,
  `taille` int(11) NOT NULL,
  `poids` double NOT NULL,
  `maladies_chroniques` varchar(255) NOT NULL,
  `group_sanguin` varchar(10) NOT NULL,
  `informations` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient`
--

INSERT INTO `patient` (`id_patient`, `taille`, `poids`, `maladies_chroniques`, `group_sanguin`, `informations`) VALUES
(83, 188, 88, 'jghjghjgh', 'B+', 'gfdfgdgfdgd');

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id_question` int(11) NOT NULL,
  `id_symptome` int(11) NOT NULL,
  `question` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id_question`, `id_symptome`, `question`) VALUES
(1, 1, 'Avez-vous de la fièvre ?'),
(2, 2, 'Ressentez-vous une fatigue générale ?'),
(3, 3, 'Avez-vous des maux de tête ?'),
(4, 4, 'Avez-vous une toux sèche ?'),
(5, 5, 'Avez-vous un écoulement nasal ?'),
(6, 6, 'Avez-vous une toux ?'),
(7, 7, 'Avez-vous des maux de gorge ?'),
(8, 8, 'Avez-vous une congestion nasale ?'),
(9, 9, 'Ressentez-vous une fatigue persistante ?'),
(10, 10, 'Avez-vous un manque d\'énergie ?'),
(11, 11, 'Avez-vous des troubles du sommeil ?'),
(12, 12, 'Avez-vous une perte d\'appétit ?'),
(13, 13, 'Avez-vous des vertiges ?'),
(14, 14, 'Ressentez-vous un essoufflement ?'),
(15, 15, 'Avez-vous une vision floue ?'),
(16, 16, 'Ressentez-vous une fatigue excessive ?'),
(17, 17, 'Avez-vous de la rétention d\'eau (gonflement) ?'),
(18, 18, 'Avez-vous des palpitations cardiaques ?'),
(19, 19, 'Ressentez-vous une douleur thoracique ?'),
(20, 20, 'Avez-vous une transpiration excessive ?'),
(21, 21, 'Avez-vous des nausées ?'),
(22, 22, 'Avez-vous des démangeaisons ?'),
(23, 23, 'Avez-vous une rougeur de la peau ?'),
(24, 24, 'Avez-vous une éruption cutanée ?'),
(25, 25, 'Avez-vous la peau sèche ?'),
(26, 26, 'Avez-vous des boutons rouges ?'),
(27, 27, 'Avez-vous des points noirs ?'),
(28, 28, 'Avez-vous la peau grasse ?'),
(29, 29, 'Ressentez-vous une douleur à la pression ?'),
(30, 30, 'Avez-vous des plaques rouges sur la peau ?'),
(31, 31, 'Avez-vous une desquamation (peau qui pèle) ?'),
(32, 32, 'Ressentez-vous une douleur abdominale ?'),
(33, 33, 'Avez-vous des ballonnements ?'),
(34, 34, 'Avez-vous perdu du poids récemment ?'),
(35, 35, 'Avez-vous une sensation de satiété précoce ?'),
(36, 36, 'Avez-vous une douleur intense d\'un côté de la tête ?'),
(37, 37, 'Avez-vous une sensibilité à la lumière ?'),
(38, 38, 'Avez-vous des vomissements ?'),
(39, 39, 'Avez-vous eu des crises convulsives ?'),
(40, 40, 'Avez-vous déjà perdu connaissance ?'),
(41, 41, 'Avez-vous des tremblements ?'),
(42, 42, 'Avez-vous des mouvements involontaires ?'),
(43, 43, 'Ressentez-vous des engourdissements ?'),
(44, 44, 'Avez-vous des difficultés à marcher ?'),
(45, 45, 'Avez-vous des problèmes de vision ?'),
(46, 46, 'Entendez-vous un sifflement lors de la respiration ?'),
(47, 47, 'Avez-vous une toux nocturne ?'),
(48, 48, 'Avez-vous des difficultés à expirer ?'),
(49, 49, 'Avez-vous une toux productive (avec expectoration) ?'),
(50, 50, 'Avez-vous un mucus épais ?'),
(51, 51, 'Ressentez-vous des douleurs articulaires ?'),
(52, 52, 'Avez-vous un gonflement des articulations ?'),
(53, 53, 'Ressentez-vous une raideur articulaire ?'),
(54, 54, 'Avez-vous une rougeur autour des articulations ?'),
(55, 55, 'Avez-vous une sensibilité au soleil ?'),
(56, 56, 'Avez-vous des douleurs osseuses ?'),
(57, 57, 'Avez-vous eu des fractures fréquentes ?'),
(58, 58, 'Avez-vous constaté une perte de hauteur ?'),
(59, 59, 'Ressentez-vous une faiblesse musculaire ?'),
(60, 60, 'Avez-vous les yeux rouges ?'),
(61, 61, 'Avez-vous un larmoiement excessif ?'),
(62, 62, 'Avez-vous des démangeaisons oculaires ?'),
(63, 63, 'Avez-vous une vision double ?'),
(64, 64, 'Avez-vous des difficultés à voir la nuit ?'),
(65, 65, 'Ressentez-vous une douleur oculaire ?'),
(66, 66, 'Avez-vous une perte progressive de la vision périphérique ?'),
(67, 67, 'Avez-vous un gonflement anormal ?'),
(68, 68, 'Avez-vous des difficultés à bouger la partie affectée ?'),
(69, 69, 'Avez-vous des ecchymoses ?'),
(70, 70, 'Ressentez-vous une sensation de craquement dans l\'articulation ?'),
(71, 71, 'Avez-vous une courbure anormale de la colonne vertébrale ?'),
(72, 72, 'Avez-vous des douleurs dorsales ?'),
(73, 73, 'Ressentez-vous une fatigue musculaire ?'),
(74, 74, 'Avez-vous des difficultés à respirer ?'),
(75, 75, 'Ressentez-vous une tristesse persistante ?'),
(76, 76, 'Avez-vous perdu l\'intérêt pour vos activités habituelles ?'),
(77, 77, 'Avez-vous de l\'insomnie ?'),
(78, 78, 'Avez-vous une inquiétude excessive ?'),
(79, 79, 'Vous sentez-vous irritable ?'),
(80, 80, 'Avez-vous des brûlures d\'estomac ?'),
(81, 81, 'Avez-vous des régurgitations ?'),
(82, 82, 'Avez-vous une toux chronique ?');

-- --------------------------------------------------------

--
-- Table structure for table `rendez_vous`
--

CREATE TABLE `rendez_vous` (
  `id_rdv` int(11) NOT NULL,
  `id_patient` int(11) NOT NULL,
  `id_medecin` int(11) NOT NULL,
  `date_heure` datetime NOT NULL,
  `statut` enum('confirmé','annulé','en attente','terminé') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rendez_vous`
--

INSERT INTO `rendez_vous` (`id_rdv`, `id_patient`, `id_medecin`, `date_heure`, `statut`) VALUES
(10, 83, 77, '2025-04-28 09:30:00', 'confirmé'),
(11, 83, 82, '2025-04-28 09:00:00', 'en attente');

-- --------------------------------------------------------

--
-- Table structure for table `symptomes`
--

CREATE TABLE `symptomes` (
  `id_symptome` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `symptomes`
--

INSERT INTO `symptomes` (`id_symptome`, `nom`) VALUES
(1, 'Fièvre'),
(2, 'Fatigue'),
(3, 'Maux de tête'),
(4, 'Toux sèche'),
(5, 'Écoulement nasal'),
(6, 'Toux'),
(7, 'Maux de gorge'),
(8, 'Congestion nasale'),
(9, 'Fatigue persistante'),
(10, 'Manque d\'énergie'),
(11, 'Troubles du sommeil'),
(12, 'Perte d\'appétit'),
(13, 'Vertiges'),
(14, 'Essoufflement'),
(15, 'Vision floue'),
(16, 'Fatigue excessive'),
(17, 'Rétention d\'eau'),
(18, 'Palpitations'),
(19, 'Douleur thoracique'),
(20, 'Transpiration excessive'),
(21, 'Nausées'),
(22, 'Démangeaisons'),
(23, 'Rougeur de la peau'),
(24, 'Éruption cutanée'),
(25, 'Peau sèche'),
(26, 'Boutons rouges'),
(27, 'Points noirs'),
(28, 'Peau grasse'),
(29, 'Douleur à la pression'),
(30, 'Plaques rouges'),
(31, 'Desquamation (peau qui pèle)'),
(32, 'Douleur abdominale'),
(33, 'Ballonnements'),
(34, 'Perte de poids'),
(35, 'Sensation de satiété précoce'),
(36, 'Douleur intense d\'un côté de la tête'),
(37, 'Sensibilité à la lumière'),
(38, 'Vomissements'),
(39, 'Crises convulsives'),
(40, 'Perte de conscience'),
(41, 'Tremblements'),
(42, 'Mouvements involontaires'),
(43, 'Engourdissements'),
(44, 'Difficulté à marcher'),
(45, 'Problèmes de vision'),
(46, 'Sifflement lors de la respiration'),
(47, 'Toux nocturne'),
(48, 'Difficulté à expirer'),
(49, 'Toux productive (avec expectoration)'),
(50, 'Mucus épais'),
(51, 'Douleur articulaire'),
(52, 'Gonflement des articulations'),
(53, 'Raideur'),
(54, 'Rougeur autour des articulations'),
(55, 'Sensibilité au soleil'),
(56, 'Douleur osseuse'),
(57, 'Fractures fréquentes'),
(58, 'Perte de hauteur'),
(59, 'Faiblesse musculaire'),
(60, 'Rougeur des yeux'),
(61, 'Larmoiement excessif'),
(62, 'Démangeaisons oculaires'),
(63, 'Vision double'),
(64, 'Difficulté à voir la nuit'),
(65, 'Douleur oculaire'),
(66, 'Perte progressive de la vision périphérique'),
(67, 'Gonflement'),
(68, 'Difficulté à bouger la partie affectée'),
(69, 'Ecchymoses'),
(70, 'Sensation de \"craquement\" dans l\'articulation'),
(71, 'Courbure anormale de la colonne vertébrale'),
(72, 'Douleur dorsale'),
(73, 'Fatigue musculaire'),
(74, 'Difficulté à respirer'),
(75, 'Tristesse persistante'),
(76, 'Perte d\'intérêt'),
(77, 'Insomnie'),
(78, 'Inquiétude excessive'),
(79, 'Irritabilité'),
(80, 'Brûlures d\'estomac'),
(81, 'Régurgitation'),
(82, 'Toux chronique');

-- --------------------------------------------------------

--
-- Table structure for table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `id_utilisateur` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `age` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `mot_de_passe` text NOT NULL,
  `telephone` varchar(11) NOT NULL,
  `type_utilisateur` enum('patient','medecin') NOT NULL,
  `photo_profil` text NOT NULL,
  `role` enum('admin','regular') NOT NULL DEFAULT 'regular',
  `date_connexion` timestamp NOT NULL DEFAULT current_timestamp(),
  `genre` enum('homme','femme') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `utilisateur`
--

INSERT INTO `utilisateur` (`id_utilisateur`, `nom`, `prenom`, `age`, `email`, `mot_de_passe`, `telephone`, `type_utilisateur`, `photo_profil`, `role`, `date_connexion`, `genre`) VALUES
(68, 'dsqdqsd', 'fedi', 0, 'toprockstar04@gmail.com', '$2y$10$UQMOuvEv2uZ4bB.VEmwzWOeDh4xZfuvQTosF4dv9ifzn5477o9UBe', '12345678', 'medecin', 'user_photos/6807b61fc85a5_Black.png', 'regular', '2025-04-14 06:13:35', 'homme'),
(72, 'chakra', 'abdelhedi', 15, 'fdsfsfsdf@gmail.com', '$2y$10$KMfdg4bIxUP/dsm4i/vrrejFnQGCwzSa7YJOPhgyeO78WpTWbAK3G', '50078199', 'medecin', 'user_photos/68064ed8d6878_online-doctor-medical-consultation-cartoon-art-illustration.png', 'regular', '2025-04-21 13:58:05', 'homme'),
(73, 'fedi', 'ghribi', 20, 'gdfgfdgfdgfdgfdgfdgfdg@gmail.com', '$2y$10$GbiOMpAR8X.yQU6T4.rhYuLZrYd5vCppsvRb79Xn/fHDeaDqe6G1a', '12345678', 'medecin', 'user_photos/68068eff13d35_Free Vector _ National doctor\'s day hand drawn background.jfif', 'regular', '2025-04-21 18:31:27', 'homme'),
(77, 'fadi', 'ghribie', 21, 'jimmysins60@gmail.com', '$2y$10$vZiry/qJI5kLMmz1R5sbNe5ytxeHf9V6T12a9gL.2Z/9s1WuReLAy', '50078199', 'medecin', 'user_photos/6807a59b91a3f_454664533_2095442917572594_3886860493663609370_n.jpg', 'admin', '2025-04-22 14:20:46', 'homme'),
(82, 'benali', 'adem', 20, 'adembenali2004@gmail.com', '$2y$10$bwbFq3A0SfLvx/pHDhWzfOy8BDFimHWKMVx/cEQSaPxX2/8MlMK2C', '27405006', 'medecin', 'user_photos/680846d48018b_aefe277c-13a4-4329-bb67-1b725d77e59d.jpg', 'regular', '2025-04-23 01:48:56', 'femme'),
(83, 'fadi', 'ghribi', 21, 'fadiyghribie@gmail.com', '$2y$10$dTYkdb8Ll8SD80abjzkWb.LOsouPgxGK2IMshEiyyYWidHfqcxoZO', '50078199', 'patient', 'user_photos/680aeee6295fd_images (1).jpg', 'regular', '2025-04-25 02:09:54', 'homme');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `calendrier_medecin`
--
ALTER TABLE `calendrier_medecin`
  ADD PRIMARY KEY (`id_calendrier`),
  ADD KEY `fk53` (`id_medecin`);

--
-- Indexes for table `diagnostic`
--
ALTER TABLE `diagnostic`
  ADD PRIMARY KEY (`id_diagnostic`),
  ADD KEY `fk12` (`id_medecin`),
  ADD KEY `fk100` (`id_rdv`);

--
-- Indexes for table `facture`
--
ALTER TABLE `facture`
  ADD PRIMARY KEY (`id_facture`),
  ADD KEY `fk33` (`id_paiement`);

--
-- Indexes for table `fiche_medicale`
--
ALTER TABLE `fiche_medicale`
  ADD PRIMARY KEY (`id_fiche`),
  ADD KEY `fk20` (`id_rdv`);

--
-- Indexes for table `historique`
--
ALTER TABLE `historique`
  ADD PRIMARY KEY (`id_consultation`),
  ADD KEY `fk4` (`id_patient`),
  ADD KEY `fk5` (`id_medecin`),
  ADD KEY `fk6` (`id_rdv`);

--
-- Indexes for table `maladies`
--
ALTER TABLE `maladies`
  ADD PRIMARY KEY (`id_maladie`);

--
-- Indexes for table `maladi_symtome`
--
ALTER TABLE `maladi_symtome`
  ADD PRIMARY KEY (`id_maladie`,`id_symptome`),
  ADD KEY `fk_symptome` (`id_symptome`);

--
-- Indexes for table `medecin`
--
ALTER TABLE `medecin`
  ADD PRIMARY KEY (`id_medecin`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`id_notification`),
  ADD KEY `fk3` (`id_utilisateur`);

--
-- Indexes for table `paiement`
--
ALTER TABLE `paiement`
  ADD PRIMARY KEY (`id_paiement`),
  ADD KEY `fk21` (`id_rdv`),
  ADD KEY `fk22` (`id_patient`);

--
-- Indexes for table `patient`
--
ALTER TABLE `patient`
  ADD PRIMARY KEY (`id_patient`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id_question`),
  ADD KEY `id_symptome` (`id_symptome`);

--
-- Indexes for table `rendez_vous`
--
ALTER TABLE `rendez_vous`
  ADD PRIMARY KEY (`id_rdv`),
  ADD KEY `fk10` (`id_medecin`),
  ADD KEY `fk9` (`id_patient`);

--
-- Indexes for table `symptomes`
--
ALTER TABLE `symptomes`
  ADD PRIMARY KEY (`id_symptome`);

--
-- Indexes for table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`id_utilisateur`),
  ADD UNIQUE KEY `unique_email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `calendrier_medecin`
--
ALTER TABLE `calendrier_medecin`
  MODIFY `id_calendrier` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `diagnostic`
--
ALTER TABLE `diagnostic`
  MODIFY `id_diagnostic` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `facture`
--
ALTER TABLE `facture`
  MODIFY `id_facture` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fiche_medicale`
--
ALTER TABLE `fiche_medicale`
  MODIFY `id_fiche` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `historique`
--
ALTER TABLE `historique`
  MODIFY `id_consultation` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `maladies`
--
ALTER TABLE `maladies`
  MODIFY `id_maladie` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `id_notification` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `paiement`
--
ALTER TABLE `paiement`
  MODIFY `id_paiement` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id_question` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `rendez_vous`
--
ALTER TABLE `rendez_vous`
  MODIFY `id_rdv` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `symptomes`
--
ALTER TABLE `symptomes`
  MODIFY `id_symptome` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id_utilisateur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `calendrier_medecin`
--
ALTER TABLE `calendrier_medecin`
  ADD CONSTRAINT `fk53` FOREIGN KEY (`id_medecin`) REFERENCES `medecin` (`id_medecin`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `diagnostic`
--
ALTER TABLE `diagnostic`
  ADD CONSTRAINT `fk100` FOREIGN KEY (`id_rdv`) REFERENCES `rendez_vous` (`id_rdv`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk12` FOREIGN KEY (`id_medecin`) REFERENCES `medecin` (`id_medecin`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `facture`
--
ALTER TABLE `facture`
  ADD CONSTRAINT `fk33` FOREIGN KEY (`id_paiement`) REFERENCES `paiement` (`id_paiement`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `fiche_medicale`
--
ALTER TABLE `fiche_medicale`
  ADD CONSTRAINT `fk20` FOREIGN KEY (`id_rdv`) REFERENCES `rendez_vous` (`id_rdv`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `historique`
--
ALTER TABLE `historique`
  ADD CONSTRAINT `fk4` FOREIGN KEY (`id_patient`) REFERENCES `patient` (`id_patient`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk5` FOREIGN KEY (`id_medecin`) REFERENCES `medecin` (`id_medecin`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk6` FOREIGN KEY (`id_rdv`) REFERENCES `rendez_vous` (`id_rdv`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `maladi_symtome`
--
ALTER TABLE `maladi_symtome`
  ADD CONSTRAINT `fk_maladie` FOREIGN KEY (`id_maladie`) REFERENCES `maladies` (`id_maladie`),
  ADD CONSTRAINT `fk_symptome` FOREIGN KEY (`id_symptome`) REFERENCES `symptomes` (`id_symptome`);

--
-- Constraints for table `medecin`
--
ALTER TABLE `medecin`
  ADD CONSTRAINT `fk2_name` FOREIGN KEY (`id_medecin`) REFERENCES `utilisateur` (`id_utilisateur`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `notification`
--
ALTER TABLE `notification`
  ADD CONSTRAINT `fk3` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `paiement`
--
ALTER TABLE `paiement`
  ADD CONSTRAINT `fk21` FOREIGN KEY (`id_rdv`) REFERENCES `rendez_vous` (`id_rdv`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk22` FOREIGN KEY (`id_patient`) REFERENCES `patient` (`id_patient`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `patient`
--
ALTER TABLE `patient`
  ADD CONSTRAINT `fk1_name` FOREIGN KEY (`id_patient`) REFERENCES `utilisateur` (`id_utilisateur`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`id_symptome`) REFERENCES `symptomes` (`id_symptome`);

--
-- Constraints for table `rendez_vous`
--
ALTER TABLE `rendez_vous`
  ADD CONSTRAINT `fk10` FOREIGN KEY (`id_medecin`) REFERENCES `medecin` (`id_medecin`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk9` FOREIGN KEY (`id_patient`) REFERENCES `patient` (`id_patient`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
