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

$badnames = array(
			"chatbot","admin","operator","self",
);

// Input-Filter
$name = $user->clean($_POST["name"]);
$name = ucfirst(strtolower($name));
$pass1 = $user->clean($_POST["securepass"]);
$pass2 = $user->clean($_POST["resecurepass"]);
$email = strtolower($user->clean($_POST["email"]));
$code = htmlentities($user->clean($_POST["code"]));
$gender = htmlentities($user->clean($_POST["gender"]));
// Filter-Patterns
$name_pattern = "^[a-zA-Z0-9_]{3,16}$^";
$email_pattern = "/^[a-z0-9&\'\.\-_\+]+@[a-z0-9\-]+\.([a-z0-9\-]+\.)*+[a-z]{2}/is";

// Datenbank abfragen
$user_exists = $user->exists($name);

if( $_POST["register"] )
{
	if( !$user_exists && $name != "" && !in_array(strtolower($name),$badnames) && preg_match($name_pattern,strtolower($name)) && preg_match($email_pattern,$email) && $pass1 != "" && $pass1 == $pass2 && ( $gender == "m" || $gender == "f" ) && $code == $_SESSION["captcha_spam"] )
	{
		$zeit = time();
		$key = $captcha->randomString(32);
		$query1 = $user->query("INSERT INTO {$db["user"]} (username,userpass,reg_date,level,`key`,status) VALUES ('{$name}',md5('{$pass1}'),'{$zeit}',1,'{$key}',3)");
	
		if($query->error == ""){
			$query2 = $user->query("INSERT INTO {$db["userdetails"]} (email,color,autoscroll,gender) VALUES ('{$email}', '#000000', '1', '{$gender}')");
		}
		if($query1->error == "" && $query2->error == ""){
			
			// Text einbinden
			$text = file_get_contents($config["lang_path"] . "/register.txt");
			
			// Text-Variablen
			$reg_vars = array();
			$reg_vars["{username}"] = $name;
			$reg_vars["{password}"] = $pass1;
			$reg_vars["{url}"] = $config["scriptpath"];
			$reg_vars["{activate_url}"] = $config["scriptpath"]. "/?site=validate&name={$name}&key=".$key;
			
			foreach($reg_vars as $key => $value){
				$text = str_replace($key,$value,$text);
			}			
			
			// E-Mail versenden
			if( !isset($config["email_aktivierung"]) || $config["email_aktivierung"] == 1 ){
				$mail->sendMail($config["admin_email"],$email,$config["overall_title"],$text);
			}
			//echo Chat::self_refresh("index.php?user=".$name,0,"_self");
			echo "<script type=\"text/javascript\"> setTimeout(\"window.open('index.php?user={$name}','_self');\",0); </script>";
			exit;
		}
	}
	elseif( $user_exists ){
			echo "<p align=\"center\">Der gewünschte Benutzername ist bereits belegt!</p>";
			$f_name = "*";
	}
	elseif( in_array(strtolower($name),$badnames) ){
		echo "<p align=\"center\">Der gewünschte Benutzername ist nicht verfügbar!</p>";
		$f_name = "*";
	}
	elseif( !$name ){
		echo "<p align=\"center\">Bitte wählen Sie einen Namen!</p>";
		$f_name = "*";
	}
	elseif( !$pass1 ){
		echo "<p align=\"center\">Bitte wählen Sie ein Passwort!</p>";
		$f_securepass = "*";
	}
	elseif( $pass1 != $pass2 ){
		echo "<p align=\"center\">Die Passwörter stimmen nicht überein!</p>";
		$f_securepass = "*";
		$f_securepass2 = "*";
	}
	elseif( !preg_match($name_pattern,strtolower($name)) ){
		echo "<p align=\"center\">Der Name enthält ungültige Zeichen oder ist zu kurz oder lang! (a-z,0-9)</p>";
		$f_name = "*";
	}
	elseif( !preg_match($email_pattern,$email) ){
		echo "<p align=\"center\">Die E-Mail-Adresse ist ungültig!</p>";
		$f_email = "*";
	}
	elseif( $gender != "m" && $gender != "f" ){
		echo "<p align=\"center\">Die wähle dein Geschlecht!</p>";
		$f_gender = "*";
	}
	elseif( $code != $_SESSION["captcha_spam"] ){
		echo "<p align=\"center\">Der Sicherheitscode ist falsch!</p>";
		$f_code = "*";
	}
}
else{
	$f_name = "* 3-16 Zeichen";
	$f_securepass = "";
	$f_securepass2 = "";
	$f_email = "";
	$f_gender = "";
	$f_code = "";
}

$vars = array(
					"{template_name}"	=>	$config["template_name"],
					"{POST.name}"		=>	$_POST["name"],
					"{POST.email}"		=>	$_POST["email"],
					"{f_checked}"		=>	( $gender == "f" )	?	"checked=\"checked\" "	:	"",
					"{m_checked}"		=>	( $gender == "m" )	?	"checked=\"checked\" "	:	"",
					"{overall_title}"	=>	$config["overall_title"],
					"{f_name}"			=>	$f_name,
					"{f_securepass}"	=>	$f_securepass,
					"{f_securepass2}"	=>	$f_securepass2,
					"{f_email}"			=>	$f_email,
					"{f_gender}"		=>	$f_gender,
					"{f_code}"			=>	$f_code,
);

################################################################
#### AB HIER NICHTS ÄNDERN !!! 
#### Teamplate einbinden und definierte Variablen ersetzen
################################################################

$open = file_get_contents($config["template_path"]. "/register.html");
      
foreach($vars as $key => $value)
{
   $open = str_replace($key,$value,$open);
}

echo $open;

?>