-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 04 déc. 2025 à 15:48
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `infra`
--

-- --------------------------------------------------------

--
-- Structure de la table `constructor`
--

CREATE TABLE `constructor` (
                               `id` int(11) NOT NULL,
                               `name` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `control_unit`
--

CREATE TABLE `control_unit` (
                                `name` varchar(255) DEFAULT NULL,
                                `serial` varchar(100) NOT NULL,
                                `id_constructor` int(11) DEFAULT NULL,
                                `model` varchar(100) DEFAULT NULL,
                                `type` varchar(50) DEFAULT NULL,
                                `cpu` varchar(100) DEFAULT NULL,
                                `ram_mb` int(11) DEFAULT NULL,
                                `disk_gb` int(11) DEFAULT NULL,
                                `id_operating_sytem` int(11) DEFAULT NULL,
                                `domain` varchar(100) DEFAULT NULL,
                                `location` varchar(100) DEFAULT NULL,
                                `building` varchar(100) DEFAULT NULL,
                                `room` varchar(50) DEFAULT NULL,
                                `macaddr` varchar(17) DEFAULT NULL,
                                `purchase_date` date DEFAULT NULL,
                                `warranty_end` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `operating_system_list`
--

CREATE TABLE `operating_system_list` (
                                         `id` int(11) NOT NULL,
                                         `name` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `screen`
--

CREATE TABLE `screen` (
                          `serial` varchar(100) NOT NULL,
                          `manufacturer` varchar(100) DEFAULT NULL,
                          `model` varchar(100) DEFAULT NULL,
                          `size_inch` decimal(4,1) DEFAULT NULL,
                          `resolution` varchar(20) DEFAULT NULL,
                          `connector` varchar(50) DEFAULT NULL,
                          `attached_to` varchar(100) DEFAULT NULL,
                          `id_constructor` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
                         `id` int(11) NOT NULL,
                         `name` varchar(255) NOT NULL,
                         `mdp` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `name`, `mdp`) VALUES
                                              (1, 'sysadmin', 'sysadmin'),
                                              (2, 'adminweb', 'adminweb'),
                                              (3, 'tech', 'tech');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `constructor`
--
ALTER TABLE `constructor`
    ADD PRIMARY KEY (`id`);

--
-- Index pour la table `control_unit`
--
ALTER TABLE `control_unit`
    ADD PRIMARY KEY (`serial`),
  ADD KEY `id_operating_sytem` (`id_operating_sytem`),
  ADD KEY `id_constructor` (`id_constructor`);

--
-- Index pour la table `operating_system_list`
--
ALTER TABLE `operating_system_list`
    ADD PRIMARY KEY (`id`);

--
-- Index pour la table `screen`
--
ALTER TABLE `screen`
    ADD PRIMARY KEY (`serial`),
  ADD KEY `id_constructor` (`id_constructor`) USING BTREE;

--
-- Index pour la table `users`
--
ALTER TABLE `users`
    ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `constructor`
--
ALTER TABLE `constructor`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `operating_system_list`
--
ALTER TABLE `operating_system_list`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `control_unit`
--
ALTER TABLE `control_unit`
    ADD CONSTRAINT `control_unit_ibfk_1` FOREIGN KEY (`id_operating_sytem`) REFERENCES `operating_system_list` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `control_unit_ibfk_2` FOREIGN KEY (`id_constructor`) REFERENCES `constructor` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `screen`
--
ALTER TABLE `screen`
    ADD CONSTRAINT `screen_ibfk_1` FOREIGN KEY (`id_constructor`) REFERENCES `constructor` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
