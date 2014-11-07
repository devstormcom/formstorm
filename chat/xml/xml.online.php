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

include_once("../system/config/config.inc.php");

$user = new User();
$channel = new Channel();

if( $user->getUserStatus() == 1 ){
	$user->updateOnlineStatus();
	echo "<div>".$channel->getOnlineUserList($channel->getChannelName()) . "</div>";
}
else{
	echo "<div></div>";
}

?>