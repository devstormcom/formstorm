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

$main = new Main();
$user = new User();
$main->getTemplate("overall_header");

if( !$user->isChatAdmin($user->getUserName()) ){
	die("<p align=\"center\">Zugriff verweigert!</p>");
}
else {
	$main->getTemplate("admin_index");
	
	switch( $_REQUEST["action"] ){
		
		default: break;
		case "channel": $main->getTemplate("admin_channel-index"); break;
		case "new_channel": $main->getTemplate("admin_channel-new"); break;
		case "edit_channel": $main->getTemplate("admin_channel-edit"); break;
		case "del_channel": $main->getTemplate("admin_channel-del"); break;
		case "user": $main->getTemplate("admin_user"); break;
		case "user_edit": $main->getTemplate("admin_user-edit"); break;
		case "chat": $main->getTemplate("admin_chat"); break;
		case "smileys": $main->getTemplate("admin_smileys"); break;
		case "update": $main->getTemplate("update"); break;	
	}	
	
	$main->getTemplate("admin_footer");
}

$main->getTemplate("overall_footer");
?>