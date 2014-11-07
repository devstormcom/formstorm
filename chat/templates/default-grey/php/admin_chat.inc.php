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

$mysqli = new Mysql();

// Chatlog löschen
if( $_REQUEST["mode"] == "chatlog" ){
	$time = strtotime("today");
	$query = $mysqli->query("DELETE FROM {$db["postings"]} WHERE `time` < '{$time}'");
	$msg = "Chatlog gelöscht!";
}
// Ende Chatlog löschen


// Unbestätigte Benutzer löschen
if( $_REQUEST["mode"] == "unconfirmed" ){

	$time = strtotime("-7 days");
	$result = $mysqli->result("SELECT u.id,u.username FROM {$db["user"]} u LEFT JOIN {$db["userdetails"]} d ON u.id=d.id WHERE u.`reg_date` <= '{$time}' AND u.`status` = '3' AND u.level < '999'");
	
	while( $row = $result->fetch_assoc() ){		
		$query = $mysqli->query("DELETE FROM `{$db["userdetails"]}` WHERE `id` = '{$row["id"]}'");
		$query = $mysqli->query("DELETE FROM `{$db["postings"]}` WHERE `username` = '{$row["username"]}'");
		$query = $mysqli->query("DELETE FROM `{$db["online"]}` WHERE `username` = '{$row["username"]}'");
		$query = $mysqli->query("DELETE FROM `{$db["user"]}` WHERE `id` = '{$row["id"]}'");
	}	
	$msg = "Unbestätigte Benutzer gelöscht!";
}
// Ende Unbestätigte Benutzer löschen


// Inaktive Benutzer löschen
if( $_REQUEST["mode"] == "inaktiv" ){

	$time = strtotime("-60 days");
	$result = $mysqli->result("SELECT u.id,u.username FROM {$db["user"]} u LEFT JOIN {$db["userdetails"]} d ON u.id=d.id WHERE u.`last_login` < '{$time}' AND u.level < '999'");
	
	while( $row = $result->fetch_assoc() ){		
		$query = $mysqli->query("DELETE FROM `{$db["userdetails"]}` WHERE `id` = '{$row["id"]}'");
		$query = $mysqli->query("DELETE FROM `{$db["postings"]}` WHERE `username` = '{$row["username"]}'");
		$query = $mysqli->query("DELETE FROM `{$db["online"]}` WHERE `username` = '{$row["username"]}'");
		$query = $mysqli->query("DELETE FROM `{$db["user"]}` WHERE `id` = '{$row["id"]}'");
	}	
	$msg = "Inaktive Benutzer gelöscht!";
}
// Ende Inaktive Benutzer löschen


$vars = array(
					"{template_name}" => $config["template_name"],
					"{msg}"	=> $msg,

);

################################################################
#### AB HIER NICHTS ÄNDERN !!! 
#### Teamplate einbinden und definierte Variablen ersetzen
################################################################

$open = file_get_contents($config["template_path"]. "/admin_chat.html");
      
foreach($vars as $key => $value)
{
   $open = str_replace($key,$value,$open);
}

echo $open;

?>