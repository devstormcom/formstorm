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

include_once("system/config/config.inc.php");

$mysqli = new Mysql();

// MySQL Update
// Version 0.7.x auf 0.7.5 Update "key"-Eintrag in der User-Tabelle hinzufügen
$query = $mysqli->query("ALTER TABLE `{$db["user"]}` ADD `key` VARCHAR( 32 ) NOT NULL AFTER `silent`",false);
// Version 0.9.0 RC auf 0.9.1 Update "chanop" und "dechanop" Rechte den Operatoren hinzufügen
$query = $mysqli->query("UPDATE `{$db["privileges"]}` SET `CHANOP` = '1', `DECHANOP` = '1' WHERE `group_id` = '3'",false);

// Version 0.9.0 auf 0.10.0-Unicode
$query = $mysqli->query("ALTER TABLE `{$db["channel"]}` CHARACTER SET utf8 COLLATE utf8_general_ci",false);
$query = $mysqli->query("ALTER TABLE `{$db["groups"]}` CHARACTER SET utf8 COLLATE utf8_general_ci",false);
$query = $mysqli->query("ALTER TABLE `{$db["online"]}` CHARACTER SET utf8 COLLATE utf8_general_ci",false);
$query = $mysqli->query("ALTER TABLE `{$db["postings"]}` CHARACTER SET utf8 COLLATE utf8_general_ci",false);
$query = $mysqli->query("ALTER TABLE `{$db["privileges"]}` CHARACTER SET utf8 COLLATE utf8_general_ci",false);
$query = $mysqli->query("ALTER TABLE `{$db["user"]}` CHARACTER SET utf8 COLLATE utf8_general_ci",false);
$query = $mysqli->query("ALTER TABLE `{$db["userdetails"]}` CHARACTER SET utf8 COLLATE utf8_general_ci",false);
$query = $mysqli->query("ALTER TABLE `{$db["channel"]}` CHANGE `channel` `channel` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
CHANGE `welcome` `welcome` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL",false);
$query = $mysqli->query("ALTER TABLE `{$db["groups"]}` CHANGE `rang` `rang` VARCHAR( 25 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
CHANGE `icon` `icon` CHAR( 1 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
CHANGE `art` `art` VARCHAR( 6 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ",false);
$query = $mysqli->query("ALTER TABLE `{$db["online"]}` CHANGE `username` `username` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
CHANGE `channel` `channel` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ",false);
$query = $mysqli->query("ALTER TABLE {$db["postings"]} CHANGE `username` `username` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
CHANGE `channel` `channel` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
CHANGE `whisperto` `whisperto` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
CHANGE `color` `color` VARCHAR( 7 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL",false);
$query = $mysqli->query("ALTER TABLE {$db["postings"]} MODIFY COLUMN `message` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL",false);
$query = $mysqli->query("ALTER TABLE `{$db["user"]}` CHANGE `username` `username` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
CHANGE `userpass` `userpass` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
CHANGE `ipadress` `ipadress` VARCHAR( 40 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
CHANGE `hostname` `hostname` VARCHAR( 40 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
CHANGE `client` `client` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
CHANGE `away` `away` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
CHANGE `key` `key` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL",false);
$query = $mysqli->query("ALTER TABLE `{$db["userdetails"]}` CHANGE `email` `email` VARCHAR( 100 ) CHARACTER SET ucs2 COLLATE ucs2_general_ci NOT NULL ,
CHANGE `color` `color` VARCHAR( 7 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
CHANGE `gender` `gender` CHAR( 1 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL",false);

// Version 1.0.0-RC1 auf 1.0.0
$query = $mysqli->query("ALTER TABLE `{$db["userdetails"]}` ADD `name` VARCHAR(50) NOT NULL, ADD `city` VARCHAR(50) NOT NULL, ADD `birthdate` DATE NULL, ADD `description` TEXT NULL",false);

// Abschluss, Weiterleiten
header("Location: ./");
exit;

?>