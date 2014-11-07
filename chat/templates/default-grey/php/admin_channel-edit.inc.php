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
$groups = "";

// Channel updaten
if( $_REQUEST["update"] != "" )
{
	$query = $user->query("UPDATE {$db["channel"]} SET `userlimit` = '$_POST[userlimit]', `moderated` = '$_POST[moderiert]', `minlevel` = '$_POST[minlevel]', `hidden` = '$_POST[versteckt]', `welcome` = '{$_POST["welcome"]}' WHERE `id` = '{$_REQUEST["ChannelID"]}'");
	
	if( $query->error == "" ){
		$output .= "<div align=\"center\">Channel editiert!</div><br />";
	}
}

$result = $user->result("SELECT * FROM {$db["channel"]} WHERE `id` = '{$_GET["ChannelID"]}' LIMIT 1");
$chan = $result->fetch_assoc();
$result = $user->result("SELECT * FROM {$db["groups"]} WHERE `art` = 'global'");

while( $row = $result->fetch_assoc() )
{
	$groups .= "<option value=\"{$row["level"]}\"";

	if( $row["level"] == $chan["minlevel"] )
		$groups .= " selected=\"selected\"";

	if( $row["level"]!=1 )
		$groups .= " onclick=\"javascript:document.form.versteckt.disabled=false\"";
	else
		$groups .= " onclick=\"javascript:document.form.versteckt.disabled=true\"";


	$groups .= ">". $row["rang"] ."</option>";
}

$vars = array(
					"{template_name}" => $config["template_name"],
					"{channel_id}" => $_REQUEST["ChannelID"],
					"{output}" => $output,
					"{channel}" => $chan["channel"],
					"{userlimit}" => $chan["userlimit"],
					"{unmoderated}" => ( $chan["moderated"] ) ? "" : "selected=\"selected\"",
					"{moderated}" => ( $chan["moderated"] ) ? "selected=\"selected\"" : "",
					"{unhidden}" => ( $chan["hidden"] ) ? "" : "selected=\"selected\"",
					"{hidden}" => ( $chan["hidden"] ) ? "selected=\"selected\"" : "",
					"{welcome}" => $chan["welcome"],
					"{ranks}" => $groups,
);

################################################################
#### AB HIER NICHTS ÄNDERN !!! 
#### Teamplate einbinden und definierte Variablen ersetzen
################################################################

$open = file_get_contents($config["template_path"]. "/admin_channel-edit.html");
      
foreach($vars as $key => $value)
{
   $open = str_replace($key,$value,$open);
}

echo $open;

?>