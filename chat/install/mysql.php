<?php
/*
	SHChat
	(C) 2006-2012 by Scripthosting.net
	http://www.shchat.net

	Free for non-commercial use:
	Licensed under the "Creative Commons 3.0 BY-NC-SA"
	http://creativecommons.org/licenses/by-nc-sa/3.0/
	
	Support-Forum: http://board.scripthosting.net/viewforum.php?f=18
	Don't send emails asking for support!!
*/

$now = time();
$sql = array();

$sql[] = "DROP TABLE IF EXISTS `{$db["channel"]}`";
$sql[] = "DROP TABLE IF EXISTS `{$db["groups"]}`";
$sql[] = "DROP TABLE IF EXISTS `{$db["online"]}`";
$sql[] = "DROP TABLE IF EXISTS `{$db["postings"]}`";
$sql[] = "DROP TABLE IF EXISTS `{$db["privileges"]}`";
$sql[] = "DROP TABLE IF EXISTS `{$db["user"]}`";
$sql[] = "DROP TABLE IF EXISTS `{$db["userdetails"]}`";

$sql[] = "CREATE TABLE IF NOT EXISTS `{$db["channel"]}` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `channel` varchar(20) NOT NULL,
  `userlimit` int(10) unsigned NOT NULL default '0',
  `moderated` tinyint(1) unsigned NOT NULL default '0',
  `minlevel` int(3) unsigned NOT NULL default '1',
  `hidden` tinyint(1) unsigned NOT NULL default '0',
  `welcome` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `channel` (`channel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1";

$sql[] = "INSERT INTO `{$db["channel"]}` (`id`, `channel`, `userlimit`, `moderated`, `minlevel`, `hidden`, `welcome`) VALUES
(1, 'Lobby', 0, 0, 1, 0, 'Willkommen in der Lobby! Die Chathilfe findest du unten.')";

$sql[] = "CREATE TABLE IF NOT EXISTS `{$db["groups"]}` (
  `group_id` int(10) unsigned NOT NULL auto_increment,
  `rang` varchar(25) NOT NULL,
  `icon` char(1) NOT NULL default '',
  `level` int(3) unsigned NOT NULL default '0',
  `art` varchar(6) NOT NULL default '',
  PRIMARY KEY  (`group_id`),
  UNIQUE KEY `level` (`level`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=6";

$sql[] = "INSERT INTO `{$db["groups"]}` (`group_id`, `rang`, `icon`, `level`, `art`) VALUES
(1, 'Benutzer', '', 1, 'global'),
(2, 'Voice', '+', 2, 'temp'),
(3, 'Operator', '@', 20, 'global'),
(4, 'Administrator', '$', 999, 'global'),
(5, 'Channel Operator', '@', 10, 'temp')";

$sql[] = "CREATE TABLE IF NOT EXISTS `{$db["online"]}` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `username` varchar(20) NOT NULL,
  `channel` varchar(20) NOT NULL,
  `level` int(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1";

$sql[] = "CREATE TABLE IF NOT EXISTS `{$db["postings"]}` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `username` varchar(20) NOT NULL,
  `message` TEXT NOT NULL,
  `channel` varchar(20) NOT NULL,
  `whisperto` varchar(20) NOT NULL,
  `color` varchar(7) NOT NULL default '',
  `time` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1";

$sql[] = "CREATE TABLE IF NOT EXISTS `{$db["privileges"]}` (
  `group_id` int(10) unsigned NOT NULL auto_increment,
  `WRITE` tinyint(1) unsigned NOT NULL default '0',
  `WHISPER` tinyint(1) unsigned NOT NULL default '0',
  `JOIN` tinyint(1) unsigned NOT NULL default '0',
  `SEP` tinyint(1) unsigned NOT NULL default '0',
  `INVITE` tinyint(1) unsigned NOT NULL default '0',
  `MODERATE` tinyint(1) unsigned NOT NULL default '0',
  `VOICE` tinyint(1) unsigned NOT NULL default '0',
  `UNVOICE` tinyint(1) unsigned NOT NULL default '0',
  `KICK` tinyint(1) unsigned NOT NULL default '0',
  `BAN` tinyint(1) unsigned NOT NULL default '0',
  `UNBAN` tinyint(1) unsigned NOT NULL default '0',
  `MOD` tinyint(1) unsigned NOT NULL default '0',
  `HIDDEN` tinyint(1) unsigned NOT NULL default '0',
  `LIMIT` tinyint(1) unsigned NOT NULL default '0',
  `SILENCE` tinyint(1) unsigned NOT NULL default '0',
  `BROADCAST` tinyint(1) unsigned NOT NULL default '0',
  `OP` tinyint(1) unsigned NOT NULL default '0',
  `DEOP` tinyint(1) unsigned NOT NULL default '0',
  `CHANOP` tinyint(1) unsigned NOT NULL default '0',
  `DECHANOP` tinyint(1) unsigned NOT NULL default '0',
  `REQUEST` tinyint(1) unsigned NOT NULL default '0',
  `CHATBOT` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=6";

$sql[] = "INSERT INTO `{$db["privileges"]}` (`group_id`, `WRITE`, `WHISPER`, `JOIN`, `SEP`, `INVITE`, `MODERATE`, `VOICE`, `UNVOICE`, `KICK`, `BAN`, `UNBAN`, `MOD`, `HIDDEN`, `LIMIT`, `SILENCE`, `BROADCAST`, `OP`, `DEOP`, `CHANOP`, `DECHANOP`, `REQUEST`, `CHATBOT`) VALUES
(1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(2, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(3, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 1, 1, 1, 1, 0, 0, 0, 1, 1, 0, 0),
(4, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(5, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)";

$sql[] = "CREATE TABLE IF NOT EXISTS `{$db["user"]}` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `username` varchar(20) NOT NULL,
  `userpass` varchar(32) NOT NULL default '',
  `reg_date` int(10) unsigned NOT NULL default '0',
  `last_login` int(10) unsigned NOT NULL default '0',
  `last_reload` int(10) unsigned NOT NULL default '0',
  `last_action` int(10) unsigned NOT NULL default '0',
  `ipadress` varchar(40) default NULL,
  `hostname` varchar(40) default NULL,
  `client` varchar(255) default NULL,
  `charcount` int(10) unsigned NOT NULL default '0',
  `chat_time` int(10) unsigned NOT NULL default '0',
  `level` int(3) unsigned NOT NULL default '0',
  `away` varchar(255) NOT NULL default '',
  `silent` int(10) unsigned NOT NULL default '0',
  `key` varchar(32) NOT NULL default '',
  `status` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1";

$sql[] = "INSERT INTO `{$db["user"]}` (`id`, `username`, `userpass`, `reg_date`, `last_login`, `last_reload`, `last_action`, `ipadress`, `hostname`, `client`, `charcount`, `chat_time`, `level`, `away`, `silent`, `key`, `status`) VALUES
(1, '{$_REQUEST["admin_name"]}', MD5('{$_REQUEST["admin_pass"]}'), '{$now}', 0, 0, 0, NULL, NULL, NULL, 0, 0, 999, '', 0, '', 0)";

$sql[] = "CREATE TABLE IF NOT EXISTS `{$db["userdetails"]}` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `email` varchar(100) NOT NULL default '',
  `color` varchar(7) NOT NULL default '',
  `autoscroll` tinyint(1) unsigned NOT NULL default '1',
  `gender` char(1) NOT NULL,
  `name` VARCHAR( 50 ) NULL,
  `city` VARCHAR( 50 ) NULL,
  `birthdate` DATE NULL,
  `description` TEXT NULL, 
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1";

$sql[] = "INSERT INTO `{$db["userdetails"]}` (`id`, `email`, `color`, `autoscroll`, `gender`) VALUES
(1, '{$_REQUEST["admin_email"]}', '#000000', 1, 'm')";

?>