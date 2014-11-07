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

$admin_panel = "";
$autoscroll_checked = "";
$colors = "";
$smileys = "";

// Admin-Panel visible?
if($user->getUserLevel() == 999 ){
	$admin_panel = '<a href="javascript:admin();">Administration</a>&nbsp;&nbsp;&nbsp;';
}
// Autoscroll enabled?
if($user->getUserAutoScroll()){
	$autoscroll_checked = 'checked="checked" ';
}
// Chat-Colors
$usercolor = $user->getUserColor();
$colors = $user->getChatColors($usercolor);
// Chat-Smileys
$smileys = $user->getSmileys(25);

$vars = array(
				"{admin_panel}"			=>	$admin_panel,
				"{autoscroll_checked}"	=>	$autoscroll_checked,
				"{colors}"				=>	$colors,
				"{smileys}"				=>	$smileys,
				"{username}"			=>	$user->getUserName(),
);

################################################################
#### AB HIER NICHTS ÄNDERN !!! 
#### Teamplate einbinden und definierte Variablen ersetzen
################################################################

$open = file_get_contents($config["template_path"]. "/post.html");
      
foreach($vars as $key => $value)
{
   $open = str_replace($key,$value,$open);
}

echo $open;

?>