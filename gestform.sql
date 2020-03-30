-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : sam. 28 mars 2020 à 11:24
-- Version du serveur :  10.4.11-MariaDB
-- Version de PHP : 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `gestform`
--

-- --------------------------------------------------------

--
-- Structure de la table `app_settings`
--

CREATE TABLE `app_settings` (
  `settings_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `settings_value` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `calendar_event`
--

CREATE TABLE `calendar_event` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_invited_id` int(11) DEFAULT NULL,
  `start_event` datetime NOT NULL,
  `end_event` datetime NOT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event_description` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title_comment` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body_comment` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_comment` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `training`
--

CREATE TABLE `training` (
  `id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `start_training` datetime NOT NULL DEFAULT current_timestamp(),
  `end_training` datetime DEFAULT NULL,
  `max_student` int(11) NOT NULL,
  `price_per_student` int(11) DEFAULT NULL,
  `training_description` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `training`
--

INSERT INTO `training` (`id`, `teacher_id`, `start_training`, `end_training`, `max_student`, `price_per_student`, `training_description`, `subject`) VALUES
(1, 1, '2020-03-25 19:05:56', '2020-03-25 19:05:56', 5, 5, 'test', 'rest'),
(2, 1, '2020-03-25 19:08:29', '2020-09-09 00:00:00', 5, 5, 'test', 'rest'),
(3, 1, '2020-03-25 19:24:34', '2020-09-09 00:00:00', 5, 5, 'test', 'rest'),
(4, 1, '2020-03-25 19:25:19', '2020-09-09 00:00:00', 5, 5, 'test', 'rest');

-- --------------------------------------------------------

--
-- Structure de la table `training_user`
--

CREATE TABLE `training_user` (
  `training_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:json)',
  `password` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `lastname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `firstname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postcode` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `email`, `roles`, `password`, `lastname`, `firstname`, `phone`, `address`, `postcode`, `city`) VALUES
(1, 'student1@user.com', '[\"ROLE_TEACHER\"]', '123', 'Student', 'ONE', '0611213141', 'rue du user', '59800', 'lille'),
(6, 'student3@user.com', '[\"ROLE_STUDENT\"]', '123', 'Studentthree', 'THREE', '0613233343', 'avenue d\'user', '59800', 'Lille'),
(58, 'boulanger-boulanger@gmail.com', '[\"ROLE_TEACHER\"]', '$argon2id$v=19$m=65536,t=4,p=1$TEd1NmNKNVRtc0hxdjU1dQ$Uo0nB2bAtmxXlxtHyPckqU6VPOtOUItDbHHpwHdnBkI', 'Boulanger', 'Sébastien', '+33 5 44 68 61 ', '782, rue Evrard', '78057', 'Lille'),
(59, 'valette-valette@gmail.com', '[\"ROLE_TEACHER\"]', '$argon2id$v=19$m=65536,t=4,p=1$TnBZam45UGtKbS9KVFhEcA$lDEO7XJBMsL/ClygtM7E1ZgGX+LczEiCs/f5HJKSHHc', 'Valette', 'Grégoire', '09 42 67 52 59', '82, chemin de Robert', '78049', 'Lille'),
(60, 'picard-picard@gmail.com', '[\"ROLE_STUDENT\"]', '$argon2id$v=19$m=65536,t=4,p=1$dGdGcmNXaUdiOVQ0T3NWRg$A7xvczZZsh+59y2t9VBOVzck5sW6Kpvvm34ZBKHF+IY', 'Picard', 'Olivier', '+33 (0)1 23 61 ', '33, rue Anne Royer', '78015', 'Lille'),
(61, 'leroy-leroy@gmail.com', '[\"ROLE_STUDENT\"]', '$argon2id$v=19$m=65536,t=4,p=1$blBLdWhaWW96ck9peGMvcA$V/QcAdNtMrLpiI4tX6TJupw1sSXjj7ZR+dm1+H/l3Xo', 'Leroy', 'Jacqueline', '+33 3 70 88 56 ', '45, rue Élodie Pires', '78117', 'Lille'),
(62, 'klein-klein@gmail.com', '[\"ROLE_STUDENT\"]', '$argon2id$v=19$m=65536,t=4,p=1$SHcucXRkZno1ai5MRGcwcA$UZpYdMXh/tdlMn7ZwWDJG5TCRdIH0rYuT/SsdAizjNE', 'Klein', 'Franck', '0452613584', '2, rue de Morin', '78005', 'Lille'),
(63, 'gomes-gomes@gmail.com', '[\"ROLE_TEACHER\"]', '$argon2id$v=19$m=65536,t=4,p=1$Tk8wWTZxUVJzSDRJdzIxSA$L1r1CPLKMDNmH6urBU8ks6IrG2rsCT/PTFxAs2ylVY8', 'Gomes', 'Hortense', '+33 4 60 61 00 ', '61, impasse Édouard Chevallier', '78070', 'Lille'),
(64, 'colin-colin@gmail.com', '[\"ROLE_TEACHER\"]', '$argon2id$v=19$m=65536,t=4,p=1$TWtkMHJMREx4dzVlaHhhTw$pbznCOxO+TULsNYgKapCZINKJ1/RKJSsWkUlcKc+Gz4', 'Colin', 'Nicolas', '0740757625', '14, place Pineau', '78048', 'Lille'),
(65, 'fournier-fournier@gmail.com', '[\"ROLE_TEACHER\"]', '$argon2id$v=19$m=65536,t=4,p=1$ZzlXOFZOa3JOdk1aVkk2QQ$oYz9wxmaPdMBkcuNsP0R8FSERu+voxT/CJITZmUaoaM', 'Fournier', 'Monique', '+33 9 17 39 25 ', '7, rue de Denis', '78068', 'Lille'),
(66, 'guillot-guillot@gmail.com', '[\"ROLE_STUDENT\"]', '$argon2id$v=19$m=65536,t=4,p=1$OWRFUXR1SnBockxheUMyNw$z9BAFUrQDN6XG6Y0ux12dfei+cB8QDTdFwICeRzY7rc', 'Guillot', 'Olivier', '01 27 37 14 46', '1, rue Guillaume Barre', '78007', 'Lille'),
(67, 'mahe-mahe@gmail.com', '[\"ROLE_TEACHER\"]', '$argon2id$v=19$m=65536,t=4,p=1$Y3R5Z201VXh2ZmNadkduRA$1b6l5j+lLy7s3T87FUj/L/GpuQYDLXxufdTOezUSjkU', 'Mahe', 'Inès', '+33 (0)6 11 32 ', '60, avenue Patrick Evrard', '78003', 'Lille'),
(68, 'dupuis-dupuis@gmail.com', '[\"ROLE_TEACHER\"]', '$argon2id$v=19$m=65536,t=4,p=1$L3lDbUVyOVM0NmdqTGNtYw$BViE2TQUHYjznHltMJFuxH3hfGhVKjvUYLXIuZq8JVw', 'Dupuis', 'Léon', '05 91 79 73 53', '46, chemin Charles Rolland', '78084', 'Lille'),
(69, 'ruiz-ruiz@gmail.com', '[\"ROLE_TEACHER\"]', '$argon2id$v=19$m=65536,t=4,p=1$UnpIMThMeHRMWnNILzlDTg$7aR3RfnbmkVkuCGriWqsbTWn7LTKrDikcNGH6sgsyCk', 'Ruiz', 'Claire', '0782718206', '510, place Léon Da Silva', '78113', 'Lille'),
(70, 'guillaume-guillaume@gmail.com', '[\"ROLE_TEACHER\"]', '$argon2id$v=19$m=65536,t=4,p=1$OENkTnpBdWlpOE5Wc3RkZQ$IZwSuvbVfa/RAjdNuXUCvGWg22vBJFTYQ0HT6zMa6oE', 'Guillaume', 'Yves', '+33 (0)8 91 19 ', '9, boulevard de Pinto', '78096', 'Lille'),
(71, 'da silva-da silva@gmail.com', '[\"ROLE_STUDENT\"]', '$argon2id$v=19$m=65536,t=4,p=1$QjA2cmdJS2dtWUtXZlVCcw$NSn1DHm7A0byj8lvTKBNmZoJeh/zu9HXSQpzh5OMVKM', 'Da Silva', 'Maggie', '+33 3 79 26 42 ', 'avenue Rossi', '78009', 'Lille'),
(72, 'gros-gros@gmail.com', '[\"ROLE_STUDENT\"]', '$argon2id$v=19$m=65536,t=4,p=1$dElvNVc0aUVzNU9HZzZNZg$zikMQq4Wy53N5as5ZQOV4YEEksvK0kUcJM2DVcuXvUY', 'Gros', 'Thérèse', '+33 7 71 78 23 ', '84, rue de Gerard', '78076', 'Lille'),
(73, 'pinto-pinto@gmail.com', '[\"ROLE_TEACHER\"]', '$argon2id$v=19$m=65536,t=4,p=1$VGxwcEtRaFJLZGY0T3NnTQ$dSE90lYsSv09JBM8CkI8dvIxqHjQYnTJvxEQGlmSErQ', 'Pinto', 'Manon', '05 63 54 22 85', '25, avenue Frédéric Roy', '78029', 'Lille'),
(74, 'collet-collet@gmail.com', '[\"ROLE_STUDENT\"]', '$argon2id$v=19$m=65536,t=4,p=1$c0hCR1c1VlVhNmkzbTNPNg$tZ47J+I13NGjg34oXNJdGXckzyw3cGygKZjNTHxYCyo', 'Collet', 'Margot', '+33 5 13 08 52 ', '89, chemin de Hardy', '78113', 'Lille'),
(75, 'denis-denis@gmail.com', '[\"ROLE_TEACHER\"]', '$argon2id$v=19$m=65536,t=4,p=1$WEluWURaTUFLWC5EOXgvRg$EGThmzD5jTCTaatNARfbg8vvmNAiNr0hEyP4EO8jfXY', 'Denis', 'Émile', '+33 8 95 51 88 ', '39, impasse Olivier Gosselin', '78104', 'Lille'),
(76, 'petitjean-petitjean@gmail.com', '[\"ROLE_STUDENT\"]', '$argon2id$v=19$m=65536,t=4,p=1$Z0g0SWU0WTNGeFV4SEhiWQ$4L8W9hyeGNQNcKYKA5afKiYyTQZzU1EhsYTKRnjvljw', 'Petitjean', 'Élisabeth', '03 37 52 09 23', '34, rue Bruneau', '78089', 'Lille'),
(78, 'hoareau-hoareau@gmail.com', '[\"ROLE_TEACHER\"]', '$argon2id$v=19$m=65536,t=4,p=1$dkY5VkJlTUU3OE1Ra25Dcw$xSd6AHcfJdvoRe6eUMmRgDyajFNqe2IQaiDq9iVW5+I', 'Hoareau', 'Gilbert', '+33 (0)5 25 52 ', '818, place de Muller', '78005', 'Lille'),
(79, 'briand-briand@gmail.com', '[\"ROLE_TEACHER\"]', '$argon2id$v=19$m=65536,t=4,p=1$bHlFQ0FOTXJMRGp1aEc3NQ$ZeGU1gX8k36EVgVF3kYDlpN6iyYFrUHhWWkrfcjE++A', 'Briand', 'Pénélope', '+33 (0)6 86 51 ', '24, avenue Claudine Launay', '78013', 'Lille'),
(81, 'lejeune-lejeune@gmail.com', '[\"ROLE_STUDENT\"]', '$argon2id$v=19$m=65536,t=4,p=1$N3dxdmFPYzluLnkuRlNDZg$OP9GSsDoWkk7OVnBVwPOiuICSf3DZhJiqtFpPr5nlU8', 'Lejeune', 'Thibault', '+33 7 99 70 23 ', '86, rue Masse', '78082', 'Lille'),
(82, 'remy-remy@gmail.com', '[\"ROLE_TEACHER\"]', '$argon2id$v=19$m=65536,t=4,p=1$SlR6YnhlM1hDQ2R4MThndg$2RF0BO5PXAaGc9lfNGv5Shb4GMDagaF0yPBdnmNleNE', 'Remy', 'Tristan', '0789199219', '10, rue Gosselin', '78090', 'Lille'),
(83, 'admin@gmail.com', '[\"ROLE_ADMIN\"]', '$argon2id$v=19$m=65536,t=4,p=1$d1U5UG5KOWw2Y25pejVQbg$3lScj6egzVM1cLJXNUVmLq5M2U98y8Sbzkms9/0dapM', 'coco', 'JOHN', '+33 (0)6 05 53 ', '30, avenue de Delattre', '78087', 'Lille');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `app_settings`
--
ALTER TABLE `app_settings`
  ADD PRIMARY KEY (`settings_code`);

--
-- Index pour la table `calendar_event`
--
ALTER TABLE `calendar_event`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_57FA09C9A76ED395` (`user_id`),
  ADD KEY `IDX_57FA09C9658A81AB` (`user_invited_id`);

--
-- Index pour la table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_5F9E962AA76ED395` (`user_id`);

--
-- Index pour la table `training`
--
ALTER TABLE `training`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_D5128A8F41807E1D` (`teacher_id`);

--
-- Index pour la table `training_user`
--
ALTER TABLE `training_user`
  ADD PRIMARY KEY (`training_id`,`user_id`),
  ADD KEY `IDX_8209910ABEFD98D1` (`training_id`),
  ADD KEY `IDX_8209910AA76ED395` (`user_id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `calendar_event`
--
ALTER TABLE `calendar_event`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `training`
--
ALTER TABLE `training`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `calendar_event`
--
ALTER TABLE `calendar_event`
  ADD CONSTRAINT `FK_57FA09C9658A81AB` FOREIGN KEY (`user_invited_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_57FA09C9A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `FK_5F9E962AA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `training`
--
ALTER TABLE `training`
  ADD CONSTRAINT `FK_D5128A8F41807E1D` FOREIGN KEY (`teacher_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `training_user`
--
ALTER TABLE `training_user`
  ADD CONSTRAINT `FK_8209910AA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_8209910ABEFD98D1` FOREIGN KEY (`training_id`) REFERENCES `training` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
