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

$mail = new Mail();
$user = new User();
$captcha = new Captcha();

// Neues Passwort generieren
if( $_REQUEST["mode"] == "check" && $_REQUEST["username"] != "" && $_REQUEST["email"] != "" && $_REQUEST["code"] == $_SESSION["captcha_spam"] ){

	$result = $user->result("SELECT u.username,u.id FROM `{$db["user"]}` u LEFT JOIN `{$db["userdetails"]}` d ON u.id=d.id WHERE u.username = '{$_REQUEST["username"]}' AND d.email = '{$_REQUEST["email"]}' LIMIT 1");
	if( $result->num_rows == 1 ){
		
		$randomString = $captcha->randomString(8);
		$row = $result->fetch_assoc();		
		$query = $user->query("UPDATE `{$db["user"]}` SET userpass = MD5('{$randomString}') WHERE id = '{$row["id"]}' LIMIT 1");
		
		// Text einbinden
		$text = file_get_contents($config["lang_path"] . "/reminder.txt");

		// Text-Variablen
		$reg_vars = array();
		$reg_vars["{username}"] = $row["username"];
		$reg_vars["{password}"] = $randomString;
		$reg_vars["{url}"] = $config["scriptpath"];
		
		foreach($reg_vars as $key => $value){
			$text = str_replace($key,$value,$text);
		}		
		
		// E-Mail versenden
		$mail->sendMail($config["admin_email"],$_REQUEST["email"],"Benutzerdaten",$text);
		$output = "Es wurde eine E-Mail mit dem neuen Passwort gesendet!";
	}	
	elseif( $_REQUEST["code"] == $_SESSION["captcha_spam"] ){
		$output = "Die Kombination aus Benutzername und E-Mail wurde nicht gefunden!";
	}
}
elseif( $_REQUEST["mode"] == "check" && $_REQUEST["username"] != "" && $_REQUEST["email"] != "" && $_REQUEST["code"] != $_SESSION["captcha_spam"] ){
	$output = "Der Sicherheitscode ist falsch!";
}
// Ende Neues Passwort generieren

$vars = array(
					"{template_name}"	=>	$config["template_name"],
					"{output}"	=> $output,
);

################################################################
#### AB HIER NICHTS ÄNDERN !!! 
#### Teamplate einbinden und definierte Variablen ersetzen
################################################################

$open = file_get_contents($config["template_path"]. "/reminder.html");
      
foreach($vars as $key => $value)
{
   $open = str_replace($key,$value,$open);
}

echo $open;

?>