<?php
/*
	SHChat
	(C) by Scripthosting.net
	http://www.shchat.net

	Free for non-commercial use:
	Licensed under the "Creative Commons 3.0 BY-NC-SA"
	http://creativecommons.org/licenses/by-nc-sa/3.0/
	
	Support-Forum: http://board.scripthosting.net/viewforum.php?f=18
	Don't send emails asking for support!!
*/

// Konfiguration laden
include_once("system/config/config.min.inc.php");

if( $_REQUEST["type"] == "" ){	
	$open = file_get_contents("install/start.html");
	echo $open;
}
elseif( $_REQUEST["type"] == "install" ){

	// Crypto Modul laden
	$crypto = new Crypto();
	
	// Versuche dem Include-Ordner Schreibrechte zu geben
	@chmod("system",0777);
	
	// Read Templates
	$templates = "";
	$d = dir("templates");
	while (false !== ($entry = $d->read())) {
		if($entry != "." && $entry != ".."){
			$templates .= "<option>".$entry."</option>\r\n";
	   }
	}
	$d->close();
	
	// Variablen
	$vars = array(
					"{basepath}" => str_replace("\\","/",dirname(__FILE__)),
					"{scriptpath}" => mb_substr( "http://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"] , 0, -25 ),
					"{servername}" => $_SERVER["SERVER_NAME"],
					"{dbhost}"	=> ($_REQUEST["dbhost"]=="") ? "localhost" : $_REQUEST["dbhost"],
					"{dbuser}"	=> $_REQUEST["dbuser"],
					"{dbpass}"	=> $crypto->encrypt($_REQUEST["dbpass"],2),
					"{dbname}"	=> $_REQUEST["dbname"],
					"{dbport}"	=> ($_REQUEST["dbport"] == "" ) ? 3306 : (int) $_REQUEST["dbport"],
					"{dbsocket}"	=> $_REQUEST["dbsocket"],
					"{templates}" => $templates,
					"{template}" => $_REQUEST["template"],
					"{lang}" => $_REQUEST["lang"],
					"{title}"	=> ($_REQUEST["title"]=="") ? "Mein Chatname" : $_REQUEST["title"],
					"{admin_name}"	=> ($_REQUEST["admin_name"]=="") ? "admin" : $_REQUEST["admin_name"],
					"{admin_email}"	=> ($_REQUEST["admin_email"]=="") ? "" : $_REQUEST["admin_email"],
					"{admin_pass}"	=> ($_REQUEST["admin_pass"]=="") ? "" : $_REQUEST["admin_pass"],
					"{admin_pass1}"	=> ($_REQUEST["admin_pass1"]=="") ? "" : $_REQUEST["admin_pass1"],
					"{error}"	=> "",
					"{disabled}" => "",
					"{serialnumber}" => "null",
					"{userlimit}" => ( $_REQUEST["userlimit"] != "" ) ? $_REQUEST["userlimit"] : 0,
	);
	
	if( !is_writable("system") ){
		$vars["{error}"] = "Der Ordner /system kann nicht beschrieben werden! Bitte setzen Sie die Schreibrechte auf 777.";
		$vars["{disabled}"] = " disabled=\"disabled\"";
	}
	
	if( $_REQUEST["submit"] && is_writable("system") ){
		
		$db_ok = ( $_REQUEST["dbhost"] != "" && $_REQUEST["dbuser"] != "" && $_REQUEST["dbname"] != "" ) ? true : false;
		$pw_ok = ( $_REQUEST["admin_pass"] != "" && $_REQUEST["admin_pass"] == $_REQUEST["admin_pass1"] ) ? true : false;
		$admin_ok = ( $_REQUEST["admin_name"] != "" && $_REQUEST["admin_email"] ) ? true : false;
		
		if( $db_ok && $pw_ok && $admin_ok ){
		
			///////////////////////
			// Datenbank anlegen //
			///////////////////////
			include_once("install/{$_REQUEST["dbtype"]}.php");
			$mysqli = new Mysql($_REQUEST["dbhost"],$_REQUEST["dbuser"],$_REQUEST["dbpass"],$_REQUEST["dbname"]);
			
			foreach( $sql as $value ){			
				$query = $mysqli->query($value);		
			}
		
			//////////////////////
			// Config schreiben //
			//////////////////////	
			$open = file_get_contents("install/config.txt");
						      
			foreach($vars as $key => $value){
			   $open = str_replace(trim($key),trim($value),$open);
			}
			$open1 = fopen("system/config/config.inc.php","w");
			$write = fwrite($open1,$open);
			$close = fclose($open1);
	
			/*
			// Zugriff sperren
			$open = @fopen("install/.htaccess","w");
			$write = @fwrite($open,"DENY FROM ALL");
			$close = @fclose($open);
			$unlink = @unlink("install.php");
			*/
			
			header("Location: index.php?user=". $_REQUEST["admin_name"]);
			exit;
		}
		elseif(!$admin_ok){
			$vars["{error}"] = "Bitte wählen Sie einen Namen und eine E-Mail Adresse!";
		}
		elseif(!$pw_ok){
			$vars["{error}"] = "Die Passwörter stimmen nicht überein oder kein Passwort gewählt!";
		}
		elseif(!$db_ok) {
			$vars["{error}"] = "Bitte alle Datenbankfelder ausfüllen!";
		}	
		elseif($mysqli->error != ""){
			$vars["{error}"] = "Fehler beim Anlegen der Datenbank: ". $mysqli->error;
		}
	}
	
	################################################################
	#### AB HIER NICHTS ÄNDERN !!! 
	#### Teamplate einbinden und definierte Variablen ersetzen
	################################################################
	
	$open = file_get_contents("install/install.html");
	      
	foreach($vars as $key => $value)
	{
	   $open = str_replace($key,$value,$open);
	}
	
	echo $open;
}


/*****************************
 *	SHChat Update Installation
*****************************/

elseif( $_REQUEST["type"] == "config" ){

	// Crypto Modul laden
	$crypto = new Crypto();
	
	// Versuche dem Include-Ordner Schreibrechte zu geben
	@chmod("system/config/config.inc.php",0777);
	
	// Read Templates
	$templates = "";
	$d = dir("templates");
	while (false !== ($entry = $d->read())) {
		if($entry != "." && $entry != ".."){
			$templates .= "<option>".$entry."</option>\r\n";
	   }
	}
	$d->close();
	
	// Variablen
	$vars = array(
					"{basepath}" => str_replace("\\","/",dirname(__FILE__)),
					"{scriptpath}" => mb_substr( "http://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"] , 0, -24 ),
					"{servername}" => $_SERVER["SERVER_NAME"],
					"{dbhost}"	=> ($_REQUEST["dbhost"]=="") ? "localhost" : $_REQUEST["dbhost"],
					"{dbuser}"	=> $_REQUEST["dbuser"],
					"{dbpass}"	=> $crypto->encrypt($_REQUEST["dbpass"],2),
					"{dbname}"	=> $_REQUEST["dbname"],
					"{dbport}"	=> ($_REQUEST["dbport"] == "" ) ? 3306 : (int) $_REQUEST["dbport"],
					"{dbsocket}"	=> $_REQUEST["dbsocket"],
					"{templates}" => $templates,
					"{template}" => $_REQUEST["template"],
					"{lang}" => $_REQUEST["lang"],
					"{title}"	=> ($_REQUEST["title"]=="") ? "Mein Chatname" : $_REQUEST["title"],
					"{admin_name}"	=> ($_REQUEST["admin_name"]=="") ? "admin" : $_REQUEST["admin_name"],
					"{admin_email}"	=> ($_REQUEST["admin_email"]=="") ? "" : $_REQUEST["admin_email"],
					"{admin_pass}"	=> ($_REQUEST["admin_pass"]=="") ? "" : $_REQUEST["admin_pass"],
					"{admin_pass1}"	=> ($_REQUEST["admin_pass1"]=="") ? "" : $_REQUEST["admin_pass1"],
					"{error}"	=> "",
					"{disabled}" => "",
					"{serialnumber}" => "null",
					"{userlimit}" => ( $_REQUEST["userlimit"] != "" ) ? $_REQUEST["userlimit"] : 0,
	);
	
	if(!is_writable("system/config/config.inc.php")){
		$vars["{error}"] = "Der Ordner /system kann nicht beschrieben werden! Bitte setzen Sie die Schreibrechte auf 777.";
		$vars["{disabled}"] = " disabled=\"disabled\"";
	}
	
	if( $_REQUEST["submit"] && is_writable("system/config/config.inc.php") ){
		
		$admin_ok = ( $_REQUEST["admin_email"] ) ? true : false;
		$db_ok = ( $_REQUEST["dbhost"] != "" && $_REQUEST["dbuser"] != "" && $_REQUEST["dbname"] != "" ) ? true : false;
		
		if( $admin_ok && $db_ok ){
	
			//////////////////////
			// Config schreiben //
			//////////////////////	
			$open = file_get_contents("install/config.txt");
						      
			foreach($vars as $key => $value){
			   $open = str_replace(trim($key),trim($value),$open);
			}
			$open1 = fopen("system/config/config.inc.php","w");
			$write = fwrite($open1,$open);
			$close = fclose($open1);
			
			header("Location: index.php");
			exit;
		}
		elseif(!$admin_ok){
			$vars["{error}"] = "Bitte wählen Sie eine E-Mail Adresse!";
		}
		elseif(!$db_ok){
			$vars["{error}"] = "Bitte tragen Sie die Datenbankinformationen ein!";
		}
	}
	
	################################################################
	#### AB HIER NICHTS ÄNDERN !!! 
	#### Teamplate einbinden und definierte Variablen ersetzen
	################################################################
	
	$open = file_get_contents("install/config.html");
	      
	foreach($vars as $key => $value)
	{
	   $open = str_replace($key,$value,$open);
	}
	
	echo $open;
}

elseif( $_REQUEST["type"] == "update" ){
	if(file_exists("install/update.php")) include_once("install/update.php");
	else echo "Es ist kein Update verfügbar!";	
}
?>