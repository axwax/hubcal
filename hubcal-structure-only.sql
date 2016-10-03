-- hubCal structure only dump
SET NAMES utf8;
SET time_zone = '+00:00';

CREATE DATABASE `hubcal`;
USE `hubcal`;

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` tinytext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `events`;
CREATE TABLE `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `feedID` int(11) NOT NULL,
  `UID` varchar(255) NOT NULL,
  `title` text NOT NULL,
  `body` mediumtext NOT NULL,
  `start` datetime NOT NULL,
  `end` datetime NOT NULL,
  `url` text NOT NULL,
  `location` text NOT NULL,
  `modified` datetime NOT NULL,
  `organizerName` text NOT NULL,
  `organizerEmail` text NOT NULL,
  `attachment` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UID` (`UID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `feeds`;
CREATE TABLE `feeds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` int(11) NOT NULL,
  `name` text NOT NULL,
  `url` varchar(255) NOT NULL,
  `source_url` varchar(255) DEFAULT NULL,
  `fetched` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- 2016-10-03 09:55:13
