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

$status = "";
$fehler = true;
$username = $user->clean($_REQUEST["name"]);
$key = $user->clean($_REQUEST["key"]);

$result = $user->result("SELECT `key` FROM {$db["user"]} WHERE `username` = '{$username}' AND `key` = '{$key}' AND `status` = '3' LIMIT 1");

while( $row = $result->fetch_assoc() ){
	
	$query = $user->query("UPDATE {$db["user"]} SET `status` = '0' WHERE `username` = '{$username}' AND `status` = '3'");
	$status = "Aktivierung erfolgreich abgeschlossen!";
	$fehler = false;
	break;
}

if( $fehler ){
	$status = "Das Benutzerkonto wurde bereits aktiviert oder ist nicht vorhanden.";
}


$vars = array(
					"{template_name}"	=>	$config["template_name"],
					"{status}" => $status,
);

################################################################
#### AB HIER NICHTS ÄNDERN !!! 
#### Teamplate einbinden und definierte Variablen ersetzen
################################################################

$open = file_get_contents($config["template_path"]. "/validate.html");
      
foreach($vars as $key => $value)
{
   $open = str_replace($key,$value,$open);
}

echo $open;

?>