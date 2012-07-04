-- phpMyAdmin SQL Dump
-- version 3.4.10.1
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le : Mer 04 Juillet 2012 à 00:12
-- Version du serveur: 5.1.49
-- Version de PHP: 5.3.10

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `reboot`
--

-- --------------------------------------------------------

--
-- Structure de la table `Log`
--

CREATE TABLE IF NOT EXISTS `Log` (
  `log_id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `log_type` varchar(127) CHARACTER SET latin1 NOT NULL,
  `log_user_id` int(255) unsigned NOT NULL DEFAULT '0',
  `log_ip` varchar(15) NOT NULL,
  `log_os` varchar(127) NOT NULL,
  `log_browser` varchar(127) NOT NULL,
  `log_request` blob NOT NULL,
  `log_description` varchar(127) NOT NULL,
  `log_trace` blob NOT NULL,
  `log_url` varchar(255) NOT NULL,
  `log_referer` varchar(255) NOT NULL,
  `log_dateCreate` datetime NOT NULL,
  PRIMARY KEY (`log_id`),
  KEY `log_user_id` (`log_user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=93 ;

-- --------------------------------------------------------

--
-- Structure de la table `Moderation`
--

CREATE TABLE IF NOT EXISTS `Moderation` (
  `moderation_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `moderation_ip` varchar(15) NOT NULL,
  `moderation_user_id` int(10) unsigned NOT NULL,
  `moderation_detail` varchar(255) NOT NULL,
  `moderation_dateCreate` datetime NOT NULL,
  PRIMARY KEY (`moderation_id`),
  KEY `moderation_user_id` (`moderation_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `User`
--

CREATE TABLE IF NOT EXISTS `User` (
  `user_id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `user_pseudo` varchar(31) NOT NULL COMMENT 'pseudo',
  `user_password` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'password',
  `user_email` varchar(63) NOT NULL COMMENT 'email',
  `user_hash` varchar(63) DEFAULT NULL COMMENT 'alphanum',
  `user_uid` varchar(63) NOT NULL COMMENT 'alphanum',
  `user_dateNaissance` date DEFAULT NULL,
  `user_avatar` varchar(31) NOT NULL DEFAULT 'default.png' COMMENT 'file',
  `user_sex` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `user_newsletter` tinyint(1) NOT NULL DEFAULT '1',
  `user_statut` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `user_dateCreate` datetime NOT NULL,
  `user_dateUpdate` datetime NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
