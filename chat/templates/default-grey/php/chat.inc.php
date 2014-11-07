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

$main = new Main();
$main->getTemplate("overall_header");
$main->getTemplate("chat_header");
$main->getTemplate("chat_online");
$main->getTemplate("chat_channel");
$main->getTemplate("chat_welcome");
$main->getTemplate("chat_content");
$main->getTemplate("smileys");
$main->getTemplate("post");
$main->getTemplate("chat_footer");
$main->getTemplate("overall_footer");
?>