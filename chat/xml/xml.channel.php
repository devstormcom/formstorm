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
$channel = new Channel();

if( $user->getUserStatus() == 1 && $channel->getMinLevel() <= $user->getUserLevel() )
{
	$channel->updateOnlineUsers();
	echo "<div><b>Channel:</b>";
	
	$liste = $channel->getChannelList( $user->getUserLevel() );
	foreach($liste as $key => $value){	
		echo " | <b><a href=\"javascript:join('". $value ."');\">". $value . "</a></b>" . "\r\n";
	}
	
	echo " |</div>";
}
?>