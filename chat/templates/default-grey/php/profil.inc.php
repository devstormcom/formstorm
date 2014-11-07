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
$mail = new Mail();
$error = false;

if( $user->getUserStatus() == 1 ){
	
	// Änderungen speichern
	if( $_REQUEST["action"] == "speichern" && $mail->validate($_REQUEST["email"]) == true && $user->checkUserLogin($_SESSION["username"],$_REQUEST["pass"]) ){
		
		// Daten ändern
		$user->editUserDetail("email", $_REQUEST["email"]);
		$user->editUserDetail("gender", $_REQUEST["gender"]);
		$user->editUserDetail("name", $_REQUEST["myName"]);
		$user->editUserDetail("city", $_REQUEST["myCity"]);
		if( trim($_REQUEST["myBirthdate"]) != "" ) $user->editUserDetail("birthdate", date("Y-m-d", strtotime($_REQUEST["myBirthdate"])));
		if( substr(trim($_REQUEST["myBirthdate"]),0,1) == "-" ) $user->editUserDetail("birthdate", "@NULL");
		$user->editUserDetail("description", $_REQUEST["aboutMe"]);
			
		// Passwort ändern
		if( $user->clean($_REQUEST["pass0"]) != "" && $user->clean($_REQUEST["pass0"]) == $user->clean($_REQUEST["pass1"]) ){
			$query = $user->query("UPDATE `{$db["user"]}` SET `userpass` = MD5('{$user->clean($_REQUEST["pass0"])}') WHERE `id` = '{$_SESSION["user_id"]}'");
		}
		elseif( $user->clean($_REQUEST["pass0"]) != "" && $user->clean($_REQUEST["pass0"]) != $user->clean($_REQUEST["pass1"]) ){
			echo "<p align=\"center\" style=\"color:#800000;\"><b>Die Passwörter stimmen nicht überein!</b></p>";
			$error = true;
		} 
	}
	elseif( $_REQUEST["action"] == "speichern" && $user->checkUserLogin($_SESSION["username"],$_REQUEST["pass"]) && $mail->validate($_REQUEST["email"]) == false ){
		echo "<p align=\"center\" style=\"color:#800000;\"><b>Die E-Mail Adresse \"{$_REQUEST["email"]}\" ist ungültig!</b></p>";
		$error = true;
	}
	elseif( $_REQUEST["action"] == "speichern" && !$user->checkUserLogin($_SESSION["username"],$_REQUEST["pass"]) ){
		echo "<p align=\"center\" style=\"color:#800000;\"><b>Das aktuelle Passwort stimmt nicht!</b></p>";
		$error = true;
	}
	
	// Abschluss
	if( $_REQUEST["action"] == "speichern" && !$error ){
		echo "<p align=\"center\"><b>Änderungen gespeichert!</b></p>";
	}
	
	$result = $user->result("SELECT * FROM {$db["user"]} u LEFT JOIN {$db["userdetails"]} d ON u.id=d.id LEFT JOIN {$db["groups"]} g ON u.level=g.level WHERE u.id = '{$_SESSION["user_id"]}' LIMIT 1");
	$row = $result->fetch_assoc();
}

$vars = array(
					"{template_name}"	=>	$config["template_name"],
					"{username}"	=> $_SESSION["username"],
					"{email}"		=> $row["email"],
					"{myName}"		=> $row["name"],
					"{myCity}"		=> $row["city"],
					"{myBirthdate}"	=> ( $row["birthdate"] != "") ? date("d.m.Y", strtotime($row["birthdate"])) : "",
					"{aboutMe}"		=> $row["description"],
					"{m_checked}"	=> ( $row["gender"] == "m" ) ? "checked=\"checked\" " : "",
					"{f_checked}"	=> ( $row["gender"] == "f" ) ? "checked=\"checked\" " : "",

);

################################################################
#### AB HIER NICHTS ÄNDERN !!! 
#### Teamplate einbinden und definierte Variablen ersetzen
################################################################

$open = file_get_contents($config["template_path"]. "/profil.html");
      
foreach($vars as $key => $value)
{
   $open = str_replace($key,$value,$open);
}

echo $open;

?>