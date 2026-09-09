-- Adminer 4.8.1 MySQL 5.5.5-10.3.11-MariaDB-1:10.3.11+maria~bionic dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `playlist`;
DROP TABLE IF EXISTS `track`;
DROP TABLE IF EXISTS `User`;
DROP TABLE IF EXISTS `playlist2track`;
DROP TABLE IF EXISTS `user2playlist`;

CREATE TABLE `playlist` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `nom` varchar(100) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `playlist` (`id`, `nom`) VALUES
(1,	'Hardcore & Frenchcore'),
(2,	'Hardstyle & Uptempo'),
(3,	'Frenchcore & Soirée'),
(4,	'Remixes & Bootlegs');

CREATE TABLE `track` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `titre` varchar(100) NOT NULL,
    `genre` varchar(30) DEFAULT NULL,
    `duree` int(3) DEFAULT NULL,
    `filename` varchar(100) DEFAULT NULL,
    `type` varchar(30) DEFAULT NULL,
    `image` VARCHAR(255) DEFAULT NULL,
    `artiste_album` varchar(100) DEFAULT NULL,
    `titre_album` varchar(100) DEFAULT NULL,
    `annee_album` int(4) DEFAULT NULL,
    `numero_album` int(11) DEFAULT NULL,
    `auteur_podcast` varchar(100) DEFAULT NULL,
    `date_podcast` date DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `track` (`id`, `titre`, `genre`, `duree`, `filename`, `type`, `image`, `artiste_album`, `titre_album`, `annee_album`, `numero_album`, `auteur_podcast`, `date_podcast`) VALUES
(1,	'BARBE NOIRE',	'Hardcore',	153,	'BARBE NOIRE.mp3',	'A',	'cover_barbe_noire.jpg',	'VIELUSOS',	'BARBE NOIRE',	2024,	1,	NULL,	NULL),
(2,	'Influenceur (Hard Version)',	'Frenchcore',	220,	'Influenceur (Hard Version).mp3',	'A',	'cover_influenceur_hard_version.jpg',	'Dr. Peacock, ascendant vierge',	'Influenceur',	2025,	2,	NULL,	NULL),
(3,	'Kino Der Toten',	'Hardcore',	181,	'Kino Der Toten.mp3',	'A',	'cover_kino_der_toten.jpg',	'VIELUSOS',	'Kino Der Toten',	2024,	3,	NULL,	NULL),
(4,	'LOUDERRR',	'Hardstyle',	283,	'LOUDERRR.mp3',	'A',	'cover_louderrr.jpg',	'RAGETRAIN',	'LOUDERRR',	2025,	4,	NULL,	NULL),
(5,	'La Strasbourgeoise',	'Frenchcore',	286,	'La Strasbourgeoise.mp3',	'A',	'cover_la_strasbourgeoise.jpg',	'Vernex, Toxic Twins, Stirex',	'La Strasbourgeoise',	2026,	5,	NULL,	NULL),
(6,	'MONGOL',	'Hardcore',	155,	'MONGOL.mp3',	'A',	'cover_mongol.jpg',	'vernex',	'MONGOL',	2024,	6,	NULL,	NULL),
(7,	'Mexico en Janvier (Lushe Remix)',	'Remix',	123,	'Mexico en Janvier (Lushe Remix).mp3',	'A',	'cover_mexico_en_janvier_lushe_remix.jpg',	'Lushe, Bigflo & Oli',	'Mexico en Janvier',	2025,	7,	NULL,	NULL),
(8,	'Pennywise (Deadly Guns Remix)',	'Hardcore',	152,	'Pennywise (Deadly Guns Remix).mp3',	'A',	'cover_pennywise_deadly_guns_remix.jpg',	'Angerfist, Deadly Guns',	'Pennywise',	2024,	8,	NULL,	NULL),
(9,	'Aria (Hard Techno Edit)',	'Hard Techno',	274,	'Aria (Hard Techno Edit).mp3',	'A',	'cover_aria_hard_techno_edit.jpg',	'Sandro Cardio, GEWOONRAVES',	'Aria (Hard Techno Edit)',	2025,	9,	NULL,	NULL);

CREATE TABLE `User` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `email` varchar(256) NOT NULL,
    `passwd` varchar(256) NOT NULL,
    `role` int(11) NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `playlist2track` (
    `id_pl` int(11) NOT NULL,
    `id_track` int(11) NOT NULL,
    `no_piste_dans_liste` int(3) NOT NULL,
    PRIMARY KEY (`id_pl`,`id_track`),
    KEY `id_track` (`id_track`),
    CONSTRAINT `playlist2track_ibfk_1` FOREIGN KEY (`id_pl`) REFERENCES `playlist` (`id`),
    CONSTRAINT `playlist2track_ibfk_2` FOREIGN KEY (`id_track`) REFERENCES `track` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `playlist2track` (`id_pl`, `id_track`, `no_piste_dans_liste`) VALUES
    (1,	1,	1),
    (1,	2,	2),
    (2,	3,	1),
    (2,	4,	2),
    (2,	9,	3),
    (3,	5,	1),
    (3,	6,	2),
    (4,	7,	1),
    (4,	8,	2);


INSERT INTO `User` (`id`, `email`, `passwd`, `role`) VALUES
    (1,	'user1@mail.com',	'$2y$12$e9DCiDKOGpVs9s.9u2ENEOiq7wGvx7sngyhPvKXo2mUbI3ulGWOdC',	1),
    (2,	'user2@mail.com',	'$2y$12$4EuAiwZCaMouBpquSVoiaOnQTQTconCP9rEev6DMiugDmqivxJ3AG',	1),
    (3,	'user3@mail.com',	'$2y$12$5dDqgRbmCN35XzhniJPJ1ejM5GIpBMzRizP730IDEHsSNAu24850S',	1),
    (4,	'user4@mail.com',	'$2y$12$ltC0A0zZkD87pZ8K0e6TYOJPJeN/GcTSkUbpqq0kBvx6XdpFqzzqq',	1),
    (5,	'admin@mail.com',	'$2y$12$JtV1W6MOy/kGILbNwGR2lOqBn8PAO3Z6MupGhXpmkeCXUPQ/wzD8a',	100);

CREATE TABLE `user2playlist` (
    `id_user` int(11) NOT NULL,
    `id_pl` int(11) NOT NULL,
    PRIMARY KEY (`id_user`,`id_pl`),
    KEY `id_pl` (`id_pl`),
    CONSTRAINT `user2playlist_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `User` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
    CONSTRAINT `user2playlist_ibfk_2` FOREIGN KEY (`id_pl`) REFERENCES `playlist` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `user2playlist` (`id_user`, `id_pl`) VALUES
    (1,	1),
    (1,	2),
    (2,	3),
    (3,	4);