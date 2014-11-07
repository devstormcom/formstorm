<?php
/*
	SHChat
	(C) 2006-2013 by Scripthosting.net
	http://www.shchat.net

	Free for non-commercial use:
	Licensed under the "Creative Commons 3.0 BY-NC-SA"
	http://creativecommons.org/licenses/by-nc-sa/3.0/
	
	Support-Forum: http://board.scripthosting.net/viewforum.php?f=18
	Don't send emails asking for support!!
*/

include_once("../system/config/config.inc.php");

$user = new User();
$chatinput = new Chatinput();

if( $user->getUserStatus() == 1 && $user->clean($_REQUEST["text"]) != "" ){
	$chatinput->runChatCommand($_REQUEST["text"]);	// Chatbefehl ausführen, wird später gesäubert (class Chatinput)
}
?>
<div></div>