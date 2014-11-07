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

$channel = new Channel();
$user = new User();

$content = '<div align="center"><img src="templates/'. $config["template_name"] .'/img/10.gif" alt="Ladebalken" /> <b>CHAT WIRD GELADEN</b></div>';


$vars = array(
					"{template_name}"	=> $config["template_name"],
					"{content}"			=> $content,
);

################################################################
#### AB HIER NICHTS ÄNDERN !!! 
#### Teamplate einbinden und definierte Variablen ersetzen
################################################################

$open = file_get_contents($config["template_path"]. "/chat_content.html");
      
foreach($vars as $key => $value)
{
   $open = str_replace($key,$value,$open);
}

echo $open;

?>