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
	
$user = new User();
$channel = new Channel();

$channel_list = "";
$user_level = $user->getUserLevel();

if( $user->getUserStatus() == 1 && $channel->getMinLevel() <= $user_level )
{
	
	$liste = $channel->getChannelList( $user_level );
	
	foreach($liste as $key => $value){	
		//$channel_list .= " | <b><a href=\"javascript:join('". $value ."');\">". $value . "</a></b> (". $channel->getOnlineUsers($value) .")" . "\r\n";
		$channel_list .= " | <b><a href=\"javascript:join('". $value ."');\">". $value . "</a></b>" . "\r\n";
	}
}


$vars = array(
					"{template_name}"	=> $config["template_name"],
					"{channel_list}"	=> $channel_list,
);

################################################################
#### AB HIER NICHTS ÄNDERN !!! 
#### Teamplate einbinden und definierte Variablen ersetzen
################################################################

$open = file_get_contents($config["template_path"]. "/chat_channel.html");
      
foreach($vars as $key => $value)
{
   $open = str_replace($key,$value,$open);
}

echo $open;

?>