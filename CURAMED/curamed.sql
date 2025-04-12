-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 12, 2025 at 10:43 AM
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
-- Table structure for table `diagnostic`
--

CREATE TABLE `diagnostic` (
  `id_diagnostic` int(11) NOT NULL,
  `id_symptome` int(11) NOT NULL,
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
  `date_creation` date NOT NULL,
  `notes_medecin` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `historique`
--

CREATE TABLE `historique` (
  `id_consultation` int(11) NOT NULL,
  `id_patient` int(11) NOT NULL,
  `id_medecin` int(11) NOT NULL,
  `id_rdv` int(11) NOT NULL,
  `date_consultation` date NOT NULL,
  `notes_medecin` double NOT NULL,
  `feedback_patient` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `medecin`
--

CREATE TABLE `medecin` (
  `id_medecin` int(11) NOT NULL,
  `specialite` varchar(20) NOT NULL,
  `adresse_cabinet` varchar(50) NOT NULL,
  `experience` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `verifecation_code` varchar(4) NOT NULL,
  `is_verif` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `rendez_vous`
--

CREATE TABLE `rendez_vous` (
  `id_rdv` int(11) NOT NULL,
  `id_patient` int(11) NOT NULL,
  `id_medecin` int(11) NOT NULL,
  `date_heure` datetime NOT NULL,
  `statut` enum('confirmé','annulé','en attente','') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `symptome`
--

CREATE TABLE `symptome` (
  `id_symptome` int(11) NOT NULL,
  `id_patient` int(11) NOT NULL,
  `description` text NOT NULL,
  `date_signalement` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `date_connexion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `diagnostic`
--
ALTER TABLE `diagnostic`
  ADD PRIMARY KEY (`id_diagnostic`),
  ADD KEY `id_diagnostic` (`id_rdv`),
  ADD KEY `fk11` (`id_symptome`),
  ADD KEY `fk12` (`id_medecin`);

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
  ADD PRIMARY KEY (`id_fiche`);

--
-- Indexes for table `historique`
--
ALTER TABLE `historique`
  ADD PRIMARY KEY (`id_consultation`),
  ADD KEY `fk4` (`id_patient`),
  ADD KEY `fk5` (`id_medecin`),
  ADD KEY `fk6` (`id_rdv`);

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
-- Indexes for table `rendez_vous`
--
ALTER TABLE `rendez_vous`
  ADD PRIMARY KEY (`id_rdv`);

--
-- Indexes for table `symptome`
--
ALTER TABLE `symptome`
  ADD PRIMARY KEY (`id_symptome`),
  ADD KEY `fk7` (`id_patient`);

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
  MODIFY `id_fiche` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `historique`
--
ALTER TABLE `historique`
  MODIFY `id_consultation` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `id_notification` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `paiement`
--
ALTER TABLE `paiement`
  MODIFY `id_paiement` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rendez_vous`
--
ALTER TABLE `rendez_vous`
  MODIFY `id_rdv` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `symptome`
--
ALTER TABLE `symptome`
  MODIFY `id_symptome` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id_utilisateur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `diagnostic`
--
ALTER TABLE `diagnostic`
  ADD CONSTRAINT `fk11` FOREIGN KEY (`id_symptome`) REFERENCES `symptome` (`id_symptome`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk12` FOREIGN KEY (`id_medecin`) REFERENCES `medecin` (`id_medecin`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_diagnostic` FOREIGN KEY (`id_rdv`) REFERENCES `rendez_vous` (`id_rdv`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `facture`
--
ALTER TABLE `facture`
  ADD CONSTRAINT `fk33` FOREIGN KEY (`id_paiement`) REFERENCES `paiement` (`id_paiement`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `fiche_medicale`
--
ALTER TABLE `fiche_medicale`
  ADD CONSTRAINT `fk20` FOREIGN KEY (`id_fiche`) REFERENCES `rendez_vous` (`id_rdv`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `historique`
--
ALTER TABLE `historique`
  ADD CONSTRAINT `fk4` FOREIGN KEY (`id_patient`) REFERENCES `patient` (`id_patient`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk5` FOREIGN KEY (`id_medecin`) REFERENCES `medecin` (`id_medecin`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk6` FOREIGN KEY (`id_rdv`) REFERENCES `rendez_vous` (`id_rdv`) ON DELETE CASCADE ON UPDATE CASCADE;

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
-- Constraints for table `rendez_vous`
--
ALTER TABLE `rendez_vous`
  ADD CONSTRAINT `fk10` FOREIGN KEY (`id_rdv`) REFERENCES `medecin` (`id_medecin`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk9` FOREIGN KEY (`id_rdv`) REFERENCES `patient` (`id_patient`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `symptome`
--
ALTER TABLE `symptome`
  ADD CONSTRAINT `fk7` FOREIGN KEY (`id_patient`) REFERENCES `patient` (`id_patient`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
