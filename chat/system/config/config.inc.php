<?php
/*
	SHChat
	(C) by Scripthosting.net
	http://www.shchat.net

	Free for non-commercial use:
	Licensed under the "Creative Commons 3.0 BY-NC-SA"
	http://creativecommons.org/licenses/by-nc-sa/3.0/
	
	Support-Forum: http://board.scripthosting.net/viewforum.php?f=18
	Don't send emails asking for support!!
*/

if($_SERVER["SERVER_NAME"] != "chat.dev-storm.local" ){
	header("Location: http://chat.dev-storm.local". $_SERVER["REQUEST_URI"]);
	exit;
}

// PHP Reporting auf Default einstellen
error_reporting(E_ALL & ~E_NOTICE);

// Die folgende Zeile konvertiert alles einheitlich in UTF-8
header("Content-Type: text/html; charset=UTF-8");

// Lege die Standard-Zeitzone fest
date_default_timezone_set("Europe/Berlin");

$config = Array(	
"host" => "localhost",
"username" => "root",
"userpass" => "f5a353e5fa0045b84481d1cec5e2f6d19586d13133a2d233c8a151ac70277ec3",
"database" => "devstorm",
"port" => 3306,
"socket" => "",
"scriptpath" => "http://chat.dev-storm.local",
"template_name" => "default-grey",
"overall_title" => "DevstormChat",
"language" => "deDE",
"admin_email" => "flaver@dev-storm.com",
"old_lines" => false,
"serialnumber" => "null",
"userlimit" => "50",
"email_aktivierung" => 1,
);


////////////////////////////////////////////////////////////////
// Ab hier nichts mehr ändern / Do not change anything below! //
////////////////////////////////////////////////////////////////

$config["basepath"] = substr(__FILE__,0,-29);
$config["template_path"] = realpath($config["basepath"]."/templates/{$config["template_name"]}/html");
$config["include_path"] = realpath($config["basepath"]."/templates/{$config["template_name"]}/php");
$config["system_path"] = realpath($config["basepath"]."/system");
$config["class_path"] = realpath($config["basepath"]."/system/class");
$config["lang_path"] = realpath($config["include_path"] . "/lang/{$config["language"]}");

// Starte eine neue Session bzw. setze eine bestehende fort
// Zusätzliche Session-Einstellungen werden gesetzt
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 1);
ini_set('session.gc_maxlifetime', 900);
if(!isset($_SESSION)) session_start();

# Mysql-Config einbinden #
include_once($config["basepath"] ."/system/mysql.inc.php");
?>