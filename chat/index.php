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

if( !file_exists("system/config/config.inc.php") ){
	header("Location: install.php");
	exit;
}
else{
	include_once("system/config/config.inc.php");
}

$site = strtolower( trim($_REQUEST["site"]) );
$main = new Main();
$main->getTemplate("overall_header");

switch($site){	
	default: $main->getTemplate("index"); break;
	case "index": $main->getTemplate("index"); break;
	case "help": $main->getTemplate("help"); break;
	case "userlist": $main->getTemplate("userlist"); break;
	case "top10": $main->getTemplate("top10"); break;
	case "whois": $main->getTemplate("whois"); break;
	case "profil": $main->getTemplate("profil"); break;
	case "whoisonline": $main->getTemplate("whoisonline"); break;
	case "register": $main->getTemplate("register"); break;
	case "validate": $main->getTemplate("validate"); break;
	case "reminder": $main->getTemplate("reminder"); break;
}

$main->getTemplate("overall_footer");
?>