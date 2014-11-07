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

// PHP Reporting auf Default einstellen
error_reporting(E_ALL & ~E_NOTICE);

// Die folgende Zeile konvertiert alles einheitlich in UTF-8
header("Content-Type: text/html; charset=UTF-8");

// Lege die Standard-Zeitzone fest
date_default_timezone_set("Europe/Berlin");

////////////////////////////////////////////////////////////////
// Ab hier nichts mehr ändern / Do not change anything below! //
////////////////////////////////////////////////////////////////

$config["basepath"] = substr(__FILE__,0,-33);
$config["system_path"] = $config["basepath"]."/system";
$config["class_path"] = $config["basepath"]."/system/class";

// Starte eine neue Session bzw. setze eine bestehende fort
// Zusätzliche Session-Einstellungen werden gesetzt
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 1);
ini_set('session.gc_maxlifetime', 900);
if(!isset($_SESSION)) session_start();

# Mysql-Config und Version einbinden #
include_once($config["basepath"] ."/system/mysql.inc.php");
include_once($config["basepath"] ."/system/version.php");
?>