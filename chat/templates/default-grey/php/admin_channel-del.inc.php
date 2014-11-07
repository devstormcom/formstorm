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

$output = "";
$channel_list = "";

if( $_POST["delc"] != "" )
{
	$query = $user->query("DELETE FROM `{$db["channel"]}` WHERE `id` = '{$_POST["delc"]}' LIMIT 1");
	if( $query->error == "" )
		$output .= "<p align=\"center\">Der Channel wurde gelöscht!</p>";
	else{
		$output .= "<p align=\"center\">{$query->error}</p>";
	}
}

$result = $user->result("SELECT * FROM {$db["channel"]}");

while( $row = $result->fetch_assoc() )
{
	$channel_list .= "<option value=\"{$row["id"]}\">{$row["channel"]}</option>";
}

$vars = array(
					"{template_name}" => $config["template_name"],
					"{output}" => $output,
					"{channel_list}" => $channel_list,
);

################################################################
#### AB HIER NICHTS ÄNDERN !!! 
#### Teamplate einbinden und definierte Variablen ersetzen
################################################################

$open = file_get_contents($config["template_path"]. "/admin_channel-del.html");
      
foreach($vars as $key => $value)
{
   $open = str_replace($key,$value,$open);
}

echo $open;

?>