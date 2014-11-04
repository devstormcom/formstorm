-- phpMyAdmin SQL Dump
-- version 4.2.10
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Erstellungszeit: 05. Nov 2014 um 00:47
-- Server Version: 5.5.38
-- PHP-Version: 5.4.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Datenbank: `devstorm`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Category`
--

CREATE TABLE `Category` (
`id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Post`
--

CREATE TABLE `Post` (
`id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `thread_id` int(11) DEFAULT NULL,
  `replay` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `created` int(11) NOT NULL,
  `modified` int(11) DEFAULT '0',
  `deleted` int(11) DEFAULT '0',
  `hidden` int(11) DEFAULT '0',
  `visible` int(11) DEFAULT '1'
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Thread`
--

CREATE TABLE `Thread` (
`id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `onlyAdmin` int(11) DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `User`
--

CREATE TABLE `User` (
`id` int(11) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `created` int(18) DEFAULT NULL,
  `last_time_online` int(18) DEFAULT NULL,
  `validated` int(11) DEFAULT '0',
  `banned` varchar(10) DEFAULT 'n',
  `admin` int(11) DEFAULT '0',
  `online` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `Category`
--
ALTER TABLE `Category`
 ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `Post`
--
ALTER TABLE `Post`
 ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `Thread`
--
ALTER TABLE `Thread`
 ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `User`
--
ALTER TABLE `User`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `Category`
--
ALTER TABLE `Category`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT für Tabelle `Post`
--
ALTER TABLE `Post`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT für Tabelle `Thread`
--
ALTER TABLE `Thread`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT für Tabelle `User`
--
ALTER TABLE `User`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;