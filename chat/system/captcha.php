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

$captcha = new Captcha();

// Alte Captchas aus der Session entfernen
unset($_SESSION['captcha_spam']);

$text = $captcha->randomString(5);  //Die Zahl bestimmt die Anzahl stellen
$rand = rand(1,6);
$img_dir = $config["basepath"] . "/system/captcha";

// Bilderzeugung
$img = ImageCreateFromPNG($img_dir . "/captcha". $rand .".png");	//Backgroundimage
$color = ImageColorAllocate($img, 0, 0, 0);	//Farbe
$ttf = "captcha/xfiles.ttf";	//Schriftart
$ttfsize = 25;	//Schriftgrösse
$angle = rand(0,5);
$t_x = rand(5,30);
$t_y = 35;

// Bildausgabe
header("Content-type: image/png; charset=UTF-8");
imagettftext($img, $ttfsize, $angle, $t_x, $t_y, $color, $ttf, $text);
imagepng($img);
imagedestroy($img);

// Ausgabe in der Session speichern
$_SESSION["captcha_spam"] = $text;
?>