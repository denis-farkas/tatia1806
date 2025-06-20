-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mar. 10 juin 2025 à 13:40
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
-- Structure de la table `event`
--

DROP TABLE IF EXISTS `event`;
CREATE TABLE IF NOT EXISTS `event` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  `date` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `event`
--

INSERT INTO `event` (`id`, `name`, `description`, `link`, `image`, `updated_at`, `date`) VALUES
(7, 'Création de l\'école de danse - Malansac', '<div>Création de l\'école de danse - Site internet Malansac</div>', 'https://www.malansac.fr/%F0%9F%99%8C-une-ecole-de-danse-ouvre-sur-malansac-%F0%9F%A9%B0/', 'creationeddslmalansac-6847e5c593a49852673158.png', '2025-06-10 07:59:01', '2024-07-12 00:00:00'),
(8, 'Création de l\'école de danse - Ouest France', '<div>Publication Ouest France sur l\'Ecole de Danse Sara Leclerc&nbsp;</div>', 'https://www.ouest-france.fr/bretagne/malansac-56220/je-souhaite-que-mes-eleves-grandissent-avec-la-danse-a-malansac-elle-ouvre-son-ecole-de-danse-a97ee08a-6534-11ef-8240-06b4a6eeb030', 'creationeddslouestfrance-6847e60ac3fe4759811618.png', '2025-06-10 08:00:10', '2024-08-28 00:00:00'),
(9, 'Trophée de la vie locale 2025', '<div>L\'école a été primée dans le cadre des Trophées de la vie locale du Crédit Agricole de Questembert.</div>', 'https://www.ouest-france.fr/bretagne/malansac-56220/a-malansac-cinq-associations-primees-aux-trophees-de-la-vie-locale-c5379aa6-0755-11f0-b7aa-47223c2ed711', 'tropheevielocale-6847e66d3dd40707599988.png', '2025-06-10 08:01:49', '2025-03-23 00:00:00'),
(10, 'Gala de danse 2025', '<div>Premier gala de danse de l\'école de danse, le 14 juin 2025, à la salle du Palis Bleu de Malasanc !</div>', NULL, 'affiche-6847e6f96d83a659916635.png', '2025-06-10 08:04:09', '2025-06-14 19:30:00');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
