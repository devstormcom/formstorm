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

$prefix = "shchat_";
$iCount = 0;

$db = Array(
		"channel"		=>	$prefix."channel".$iCount,
		"groups"		=>	$prefix."groups".$iCount,
		"online"		=>	$prefix."online".$iCount,
		"postings"		=>	$prefix."postings".$iCount,
		"privileges"	=>	$prefix."privileges".$iCount,
		"user"			=>	$prefix."user".$iCount,
		"userdetails"	=>	$prefix."userdetails".$iCount
);

// PHP das Klassenverzeichnis mitteilen
function __autoload( $class_name ){
	global $config;
	require_once( $config["class_path"] . "/class.{$class_name}.php" );
}
?>