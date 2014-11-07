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

// Gruppen holen
$result = $user->result("SELECT * FROM {$db["groups"]} WHERE `art` = 'global'");

while( $row = $result->fetch_assoc() )
{
	$groups .= "<option value=\"". $row["level"] ."\"";

	if( $row["level"] !=1 )
		$groups .= " onclick=\"javascript:document.form.versteckt.disabled=false\"";
	else
		$groups .= " onclick=\"javascript:document.form.versteckt.disabled=true\"";

	$groups .= ">". $row["rang"] ."</option>";
}

if($_POST["name"])
{
	$welcome = $user->clean($_POST["welcome"]);
	$new_channel = $user->clean($_POST["name"]);
	$new_channel = str_replace("\'","",$new_channel);

	$query = $user->query("INSERT INTO {$db["channel"]} VALUES ('', '$new_channel', '$_POST[userlimit]', '$_POST[moderiert]', '$_POST[minlevel]', '$_POST[versteckt]', '$welcome') ");
	if( $query_error == "" ){
		$output .= "<div align=\"center\">Der Channel <b>". $new_channel ."</b> wurde erstellt!</div><br />";
	}
	else{
		$output .= "<div align=\"center\">Ein Channel mit dem Namen <b>". $new_channel ."</b> existiert bereits!</div><br />";
	}
}

$vars = array(
					"{template_name}" => $config["template_name"],
					"{ranks}" => $groups,
					"{output}" => $output,

);

################################################################
#### AB HIER NICHTS ÄNDERN !!! 
#### Teamplate einbinden und definierte Variablen ersetzen
################################################################

$open = file_get_contents($config["template_path"]. "/admin_channel-new.html");
      
foreach($vars as $key => $value)
{
   $open = str_replace($key,$value,$open);
}

echo $open;

?>