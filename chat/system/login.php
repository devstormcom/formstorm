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

include_once("../system/config/config.inc.php");

$user = new User();
$chatinput = new Chatinput();

// Formular-Daten auslesen und säubern
$name = $user->clean($_POST["name"]);
$name = ucfirst(strtolower($name));
$pass = $user->clean($_POST["securepass"]);
$startchannel = $user->clean($_POST["startchannel"]);
$isChatAdmin = $user->isChatAdmin($name);
//$mode = $user->clean($_POST["mode"]);

// Statusabfragen
$status = $user->checkUserLogin( $name, $pass );	// Login-Status abfragen
$channel_exists = $chatinput->channelExists($startchannel);	// Prüfen, ob der gewählte Channel existiert
$isJoinable = $chatinput->isJoinable( $startchannel, $user->getUserLevel());	// Prüfen, ob der gewählte Channel "joinable" ist
$isFull = ( $chatinput->isFull($startchannel) && !$isChatAdmin );
$limit_reached = ( $config["userlimit"] > 0 && $chatinput->getAllOnlineUsers() >= $config["userlimit"] && !$isChatAdmin  ) ? true : false;

if( $status == 0 )	// Benutzer existiert nicht
{
	session_destroy();
	header("Location: ../index.php?error");
	exit;
}
elseif( $status == 1 && $channel_exists && $isJoinable && !$isFull && !$limit_reached )
{
	$chatinput->setChannelId($startchannel);	// Aktuellen Channel auf ID setzen
	$chatinput->addUser( $user->getUserName(true), $user->getUserLevel() );	// User dem Startchannel hinzufügen
	$user->setLastLogin();	// Letzten Login des Users auf NOW() setzen
	$chatinput->updateOnlineUsers();
	$chatinput->addSystemMsg( "<i><b>". $user->getUserName() . "</b> betritt den Chat!</i>" );

	// In den Chat weiterleiten
	header("Location: ../chat.php");
	exit;
}
elseif( $status == 2 )
{
	session_destroy();
	header("Location: ../index.php?banned");
	exit;	
}
elseif( $status == 3 )
{
	session_destroy();
	header("Location: ../index.php?inactive");
	exit;	
}
elseif( !$channel_exists || !$isJoinable ){
	session_destroy();
	header("Location: ../index.php?channel_does_not_exist");
	exit;
}
elseif( $isFull ){
	session_destroy();
	header("Location: ../index.php?channel_is_full");
	exit;
}
elseif( $limit_reached ){
	session_destroy();
	header("Location: ../index.php?chat_is_full");
	exit;
}
?>