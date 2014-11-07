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

$current_version = @file_get_contents("http://download.scripthosting.net/shchat/update/index.php");
include_once($config["basepath"] . "/system/version.inc.php");

if( $_REQUEST["update"] == "1" ){
	if( $current_version == "" ){
		$info = "Die Onlineversion konnte nicht überprüft werden!&nbsp;&nbsp;&nbsp;<img src=\"templates/{$config["template_name"]}/img/item.png\" alt=\"item\" /> <a href=\"http://www.shchat.net/?site=download\" target=\"_blank\">Selber prüfen!</a>";	
	}
	elseif( BUILD < $current_version ){
		$info = "Es ist online eine neuere Version verfügbar!&nbsp;&nbsp;&nbsp;<img src=\"templates/{$config["template_name"]}/img/item.png\" alt=\"item\" /> <a href=\"http://www.shchat.net/?site=download\" target=\"_blank\">Jetzt herunterladen!</a>";
	}
	else{
		$info = "Sie besitzen bereits die aktuellste Version! Es ist kein Update notwendig.";
	}
}
else{
	$info = "Wenn Sie nach Updates suchen, wird eine Verbindung zum Updateserver hergestellt. Dies dient lediglich der Versionsprüfung und es werden dabei keine Benutzer-Informationen übermittelt oder gespeichert.<br /><br /><div align='center'><input type='button' value='Updates suchen' onclick=\"document.location.href='?action=update&amp;update=1'\" /></div>";
}

$vars = array(
			"{info}" => $info,
);

################################################################
#### AB HIER NICHTS ÄNDERN !!! 
#### Teamplate einbinden und definierte Variablen ersetzen
################################################################

$open = file_get_contents($config["template_path"]. "/update.html");
      
foreach($vars as $key => $value)
{
   $open = str_replace($key,$value,$open);
}

echo $open;

?>