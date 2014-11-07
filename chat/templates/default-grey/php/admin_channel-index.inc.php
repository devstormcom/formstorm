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

// Channel-Liste holen
$output = "";
$i=1;
$result = $user->result("SELECT * FROM {$db["channel"]} c LEFT JOIN {$db["groups"]} g ON c.minlevel=g.level");

while( $row = $result->fetch_assoc() )
{
	$row["moderated"] = ( $row["moderated"] ) ? "ja" : "nein";
	$row["hidden"] = ( $row["hidden"] ) ? "ja" : "nein";
	$mod = $i%2;

	$output .= "<tr>";
	$output .= "<td width=\"20%\" class=\"admin_menu{$mod}\"><a href=\"?action=edit_channel&amp;ChannelID=". $row["id"] ."\">". $row["channel"] ."</a></td>";
	$output .= "<td width=\"20%\" class=\"admin_menu{$mod}\">". $row["userlimit"] ."</td>";
	$output .= "<td width=\"20%\" class=\"admin_menu{$mod}\">". $row["rang"] ."</td>";
	$output .= "<td width=\"20%\" class=\"admin_menu{$mod}\">". $row["moderated"] ."</td>";
	$output .= "<td width=\"20%\" class=\"admin_menu{$mod}\">". $row["hidden"] ."</td>";
	$output .= "</tr>";
	$i++;			
}

$vars = array(
					"{template_name}" => $config["template_name"],
					"{channel_list}" => $output,

);

################################################################
#### AB HIER NICHTS ÄNDERN !!! 
#### Teamplate einbinden und definierte Variablen ersetzen
################################################################

$open = file_get_contents($config["template_path"]. "/admin_channel-index.html");
      
foreach($vars as $key => $value)
{
   $open = str_replace($key,$value,$open);
}

echo $open;

?>