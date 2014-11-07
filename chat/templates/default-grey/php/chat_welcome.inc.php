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

if( date("H")<="11" ){
	$welcome = "Guten Morgen";
}
elseif( date("H")<="16" ){
	$welcome = "Guten Tag";
}
else {
	$welcome = "Guten Abend";
}


$vars = array(
					"{template_name}" 	=> $config["template_name"],
					"{welcome}"			=> $welcome,
					"{channel_welcome}"	=> $channel->getWelcome(),
					"{username}"		=> $user->getUserName(),
					"{curtime}"			=> date("H:i"),
);

################################################################
#### AB HIER NICHTS ÄNDERN !!! 
#### Teamplate einbinden und definierte Variablen ersetzen
################################################################

$open = file_get_contents($config["template_path"]. "/chat_welcome.html");
      
foreach($vars as $key => $value)
{
   $open = str_replace($key,$value,$open);
}

echo $open;

?>