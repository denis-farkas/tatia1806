-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : jeu. 29 mai 2025 à 08:14
-- Version du serveur : 9.1.0
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `danse`
--

-- --------------------------------------------------------

--
-- Structure de la table `cours`
--

DROP TABLE IF EXISTS `cours`;
CREATE TABLE IF NOT EXISTS `cours` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `age` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `salle` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` double NOT NULL,
  `day` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `schedule` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `cours`
--

INSERT INTO `cours` (`id`, `name`, `age`, `description`, `salle`, `price`, `day`, `schedule`) VALUES
(1, 'Eveil', '4-5 ans', 'Ce cours est destiné aux enfants en moyenne/grande section.', 'Palis Bleu', 180, 'Mercredi', '10h15 à 11h'),
(2, 'Eveil', '4-5 ans', 'Ce cours est destiné aux enfants en moyenne/grande section.', 'Village des enfants', 180, 'Samedi', '14h15 à 15h00'),
(3, 'Initiation', '6-7 ans', 'Ce cours est destiné aux enfants en CP/CE1.', 'Palis Bleu', 190, 'Mercredi', '11h00 à 12h00'),
(4, 'Initiation', '6-7 ans', 'Ce cours est destiné aux enfants en CP/CE1.', 'Centre associatif', 190, 'Mercredi', '13h45 à 14h45'),
(5, 'Initiation', '6-7 ans', 'Ce cours est destiné aux enfants en CP/CE1.', 'Village des enfants', 190, 'Samedi', '15h00 à 16h00'),
(6, 'Classique', 'À partir de 8 ans', 'Cours de classique', 'Centre associatif', 200, 'Mercredi', '14h45 à 15h45'),
(7, 'Classique', 'Ados/Adultes', 'Cours de classique', 'Village des enfants', 200, 'Samedi', '16h00 à 17h15'),
(8, 'Contemporain', 'À partir de 8 ans', 'Cours de contemporain', 'Centre associatif', 200, 'Mercredi', '15h45 à 16h45'),
(9, 'Contemporain', 'Ados/Adultes', 'Cours de contemporain', 'Village des enfants', 200, 'Samedi', '17h15 à 18h30');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
