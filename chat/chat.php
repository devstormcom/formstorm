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

include_once("system/config/config.inc.php");
require_once($config["class_path"]."/class.Main.php"); 

if( $_SESSION["user_id"] == "" ){
	header("Location: system/logout.php");
	exit;
}

$main = new Main();
$main->getTemplate("chat");

?>