<?php
/*
	SHChat
	(C) 2006-2013 by Scripthosting.net
	http://www.shchat.net

	Free for non-commercial use:
	Licensed under the "Creative Commons 3.0 BY-NC-SA"
	http://creativecommons.org/licenses/by-nc-sa/3.0/
	
	Support-Forum: http://board.scripthosting.net/viewforum.php?f=18
	Don't send emails asking for support!!
*/

class Mysql {
	
	const MYSQL_VERSION = 1003;
	
	// Instanzvariablen festlegen
	private $isConnected = false;	// Boolean zur Ausgabe, ob eine Verbindung zur Datenbank fehlerfrei hergestellt wurde (Startwert: false)
	private $query_counter = 0;		// Query-Zähler für alle fehlerfrei ausgeführten Queries während einer Instanzierung
	private $query_exec = Array();	// Query-Array, in das alle fehlerfrei ausgeführten Queries während einer Instanzierung geschrieben werden
	private $host,$username,$userpass,$database,$port,$socket,$returnerror=true,$autocommit=true;		// Verbindungsvariablen für diese Instanz
	private $dbCon = null;			// Speichere die DB-Connection in einer Variablen
	private $results = Array();		// Läd alle Results in ein Array
	private $errors = Array();		// Läd alle stillen Errors in ein Array
	private $config = Array();		// Laden der Konfigurationseinstellungen aus $config in eine Instanzvariable
	private $logPath = null;		// Festlegen des Pfades für Logdateien
	
	
	/**
	 * Construct the MySQL Connection
	 * @param String $host
	 * @param String $username
	 * @param String $userpass
	 * @param String $database
	 * @param int $port [3306]
	 * @param String $socket
	 * @param Boolean $autocommit [true]
	 * @throws SQLException
	 * @return void
	 */
	public function __construct( $host = "", $username = "", $userpass = "", $database = "", $port = 3306, $socket = "", $autocommit = true ){
		
		// $config aus 'system/config/config.inc.php' einbeziehen und in die Instanzvariable $this->config schreiben
		global $config;
		$this->config = $config;		
		// $config aus dem Speicher entfernen
		unset($config);
		
		// Wenn ein Hostname übergeben wurde, verwende diese Einstellungen
		if( $host != "" ){
			$this->host = $host;
			$this->username = $username;
			$this->userpass = $userpass;
			$this->database = $database;
			$this->port = $port;
			$this->socket = $socket;
			$this->autocommit = (Boolean) $autocommit;
		}
		// Alternativ versuchen die Datenbankeinstellungen aus $config zu nehmen
		else{			
			if( isset($this->config["host"]) ){		
				// Crypto-Modul zur Datenentschlüsselung laden
				$crypto = new Crypto();

				// Einstellungen aus $config nehmen
				$this->host = $this->config["host"];
				$this->username = $this->config["username"];
				$this->userpass = $crypto->decrypt($this->config["userpass"],2);
				$this->database = $this->config["database"];
				$this->port = $this->config["port"];
				$this->socket = $this->config["socket"];
			}else{
				// Wenn $config auch keine Daten hergibt, bleibt nichts anderes übrig, als eine Ausnahme zu werfen
				throw new SQLException("Could not find database settings.");
			}
		}
	}
	
	
	/**
	 * Connect to the MySQL Database
	 * @return $mysqli Connection Object
	 */
	private function connect(){
		
		// Falls der Konstruktor nicht aufgerufen wurde, hole dies nun nach!
		if( $this->host == "" || $this->database == "" ){
			$this->__construct();
		}
		
		// Falls bereits eine Verbindung besteht, wird diese zurückgegeben, anstatt eine neue aufzubauen
		if( $this->isConnected == true && $this->dbCon != null ){
			return $this->dbCon;
		}
		else{
			// Aufbau einer Verbindung zu MySQL
			$mysqli = @new Mysqli($this->host,$this->username,$this->userpass,$this->database,$this->port,$this->socket);
			
			// Wenn keine Verbindung zur Datenbank aufgebaut werden kann, wird eine neutrale Meldung ausgegeben (Error -1)
			if( $mysqli->connect_error != "" && $this->returnerror == true ){
				echo "<div style=\"font-family:Arial,Verdana,sans-serif;font-size:20px;\">\r\n\t<b>Die gewünschte Webseite ist momentan nicht erreichbar. Bitte versuchen Sie es später erneut</b> ({$mysqli->connect_error}). \r\n</div>";
				exit;
			}else{
				if( $mysqli->connect_error == "" ){
					// Teile dem MySQL-Server mit, dass wir ihm UTF-8 Code schicken !! Er wandelt es sonst selbst nochmal um.
					@$mysqli->query("SET NAMES 'UTF8'");
					
					if( !$this->autocommit ){
						$this->setAutoCommitState(false);
					}
				}
				// Speichere die Verbindung in dbCon
				$this->dbCon = $mysqli;
			}

			// Bool'sche Prüfvariable, ob eine Verbindung aufgebaut wurde
			$this->isConnected = ( $mysqli != null && $mysqli->error == "" && $mysqli->connect_error == "" ) ? true : false;
	
			// Rückgabe der MySQL-Verbindung
			return $mysqli;
		}
	}
	
	
	/**
	 * Sendet einen MySQLiQuery ab
	 * @param String $sql
	 * 	Der auszuführende SQL-Befehl
	 * @param Boolean $error
	 * 	Ausgabe von Fehlern festlegen
	 * @return Object MySQLiQuery
	 */
	public function query( $sql, $error = true ){

		// Aufbau der Datenbankverbindung
		$mysqli = $this->connect();
			
		// Absenden der MySQL-Anfrage an den Server
		// Wenn $error true ist (Standard), wird mit Ausgabe der Fehlermeldung abgebrochen
		if($error){
			// Falls ein Fehler während der Abfrage auftaucht, muss dies abgefangen werden
			if( ($query = $mysqli->query( $sql )) == false ) {
				$err = $mysqli->error;
				// Bei Transaktionssicheren Datenbanken wird bei einem Fehler ein Rollback durchgeführt
				if( $this->autocommit == false ){
						$this->dbCon->rollback();
				}
				// Versuche den Fehler zu protokollieren
				$this->logError("MySQL meldete einen Fehler: {$err} in SQL-Statement: {$sql}");
				// Ausgabe des SQL-Fehlers auf den Bildschirm
				die("<p><b>MySQL meldete einen Fehler:</b> <span style=\"color:#FF0000;\">{$err}</span> in <b>SQL-Statement:</b> <span style=\"color:#000080;\">{$sql}</span></p>");
				//throw new SQLQueryException("MySQL meldete einen Fehler: {$err} in SQL-Statement: {$sql}");
			}
		}else{
			// Sammeln der Fehler im Array $errors
			if( ($query = $mysqli->query( $sql )) == null ){
				$this->errors[] = $mysqli->error;
			}
		}
		
		// Erhöhe den Query-Zähler
		$this->query_counter += 1;
		// Schreibe den ausgeführten Query in das Query-Array
		$this->query_exec[] = $sql;
		
		// Rückgabe der MySQL-Verbindung
		return $mysqli;
	}
	
	
	/**
	 * Gibt ein MySQLiResult zurück
	 * @param String $sql
	 * 	Der auszuführende SQL-Befehl
	 * @param Boolean $error
	 * 	Ausgabe von Fehlern festlegen
	 * @return Object MySQLiResult
	 */
	public function result( $sql, $error = true ){

		// Aufbau der Datenbankverbindung
		$mysqli = $this->connect();

		// Absenden der MySQL-Anfrage an den Server
		// Wenn $error true ist (Standard), wird mit Ausgabe der Fehlermeldung abgebrochen 
		if($error){
			// Falls ein Fehler während der Abfrage auftaucht, muss dies abgefangen werden
			if( ($result = $mysqli->query( $sql )) == false ){
				// Versuche den Fehler zu protokollieren
				$this->logError("MySQL meldete einen Fehler: {$mysqli->error} in SQL-Statement: {$sql}");
				// Ausgabe des SQL-Fehlers auf den Bildschirm
				die("<p><b>MySQL meldete einen Fehler:</b> <span style=\"color:#FF0000;\">{$mysqli->error}</span> in <b>SQL-Statement:</b> <span style=\"color:#000080;\">{$sql}</span></p>");
				//throw new SQLResultException("MySQL meldete einen Fehler: {$mysqli->error} in SQL-Statement: {$sql}");
			}
		}else{
			// Sammeln der Fehler im Array $errors
			if( ($result = $mysqli->query( $sql )) == null ){
				$this->errors[] = $mysqli->error;
			}
		}
		
		// Erhöhe den Query-Zähler
		$this->query_counter += 1;
		// Schreibe den ausgeführten Query in das Query-Array
		$this->query_exec[] = $sql;
		// Schreibe das Result in das $results Array
		$this->results[] = $result;
		
		// Rückgabe des MySQL-Resultats der Anfrage
		return $result;
	}
	
	
	/**
	 * Gibt ein einzelnes $result als Array zurück
	 * @param String $sql
	 * 	Der auszuführende SQL-Befehl
	 * @return Array
	 */
	public function resultRow( $sql ){
		$result = $this->result($sql);
		if( $this->numRows($result) != 0 ){
			// Es liegt ein Ergebnis vor!
			return $this->fetchAssoc($result);
		}
		// Es liegt kein Ergebnis vor!
		return Array();
	}
	
	
	/**
	 * Bereinigt einen String für die Verwendung in SQL-Queries
	 * @param String $string
	 * @param Boolean [$decode] - Dekodiert einen base64 kodierten String
	 * @param Boolean [$entities] - Wandelt alle HTML-Zeichen in ihr &xxxx; XML-sicheres Pendant um (Benötigt die Klasse XML!)
	 * @return String
	 */
	public function clean( $string, $decode = false, $entities = false ){

		// Schritt 1: Entferne alle Leerzeichen und Backslashes am Anfang und am Ende
		// Typecast String auf $string
		(String) $string = trim($string);
		
		// Schritt 2: Optional -> Javascript kodiert aus Charsetgründen alle Daten mit base64
		// Daher müssen zuvor alle Strings, die von Javascript verschickt wurden dekodiert werden
		if( $decode == true ) { 
			$string = ( function_exists('get_magic_quotes_gpc') && (int) get_magic_quotes_gpc() == 1 ) ?
				addslashes(trim(base64_decode($string))) : trim(base64_decode($string));
		}

		// Schritt 3: Füge \Backslashes\ zu Anführungszeichen hinzu, falls magic_quotes_gpc deaktiviert oder nicht (mehr) vorhanden ist
		// Wir schützen somit den SQL-Query vor SQL-Injections ! 
		if( !function_exists('get_magic_quotes_gpc') || (function_exists('get_magic_quotes_gpc') && (int) get_magic_quotes_gpc() == 0)) $string = addslashes($string);

		// Schritt 4: Optional -> Wandle HTML-Code im String so um, dass er nur noch ausgegeben, aber nichtmehr ausgeführt werden kann
		// Übrig gebliebene Zeichen (< > & ' "), die uns den (X)HTML-Code zerstören können, werden hier herausgefiltert
		if ( $entities == true ) { $xml = new XML(); $string = $xml->xmlSpecialChars($string); }
		
		// Schritt 5: Finale Rückgabe des bereinigten Strings zur sicheren Verwendung als SQL-Query oder für die Ausgabe in HTML
		return $string;
	}
	
	
	/**
	 * Datenbank Funktionstest
	 * Gibt im Fehlerfall false zurück
	 * @return Boolean
	 */
	public function connectionTest(){
	
		// Es sollen hier keine Fehler ausgegeben werden!
		$this->returnerror = false;
		// Alle Abfragen sollen automatisch committet werden!
		$this->setAutoCommitState(true);
				
		// Schritt 1/4: Erstelle eine Test-Tabelle in der aktuellen Datenbank ($this->database)
		$query = @$this->query("CREATE TABLE IF NOT EXISTS `{$this->database}`.`test_db` (`test_id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT, PRIMARY KEY (`test_id`)) ENGINE = InnoDB;",false);

		// Schritt 2/4: Schreibe einen Test-Datensatz in die Test-Tabelle
		$query = @$this->query("INSERT INTO `{$this->database}`.`test_db` (test_id) VALUES ( 1 );",false);		

		// Schritt 3/4: Erzeuge ein ResultSet mit einer Test-Abfrage aus der Test-Tabelle
		$result = @$this->result("SELECT test_id FROM `{$this->database}`.`test_db` WHERE test_id = 1",false);	

		// Schritt 4/4: Nur wenn ein Ergebnis vorliegt, kann die Verbindung als erfolgreich angesehen werden
		if( $this->numRows($result) > 0 ){			
			// Test-Datensatz wieder löschen
			$query = @$this->query("DROP TABLE `{$this->database}`.`test_db`",false);
			return true;
		}
		// Anderenfalls kam keine korrekte Verbindung zustande
		return false;
	}
	
	
	/**
	 * Gibt die MySQL-Server Version aus
	 * @return String
	 */
	public function getVersion(){
		$resultRow = $this->resultRow("SELECT VERSION() version");
		return (String) $resultRow["version"];
	}
	
	
	/**
	 * Gibt die Anzahl an Resultaten eines ResultSets zurück
	 * @param Object 'MySQLiResult' $result
	 * @return int
	 */
	public function numRows( $result ){
		return (int)$result->num_rows;
	}
	
	
	/**
	 * Gibt ein Mixed Array eines ResultSets zurück
	 * @param Object 'MySQLiResult' $result
	 * @return Mixed[]
	 */
	public function fetchAssoc( $result ){
		return $result->fetch_assoc();
	}
	
	
	/**
	 * Gibt die zuletzt eingefügte ID zurück
	 * @param Object 'MySQLiQuery' $query
	 * @return int
	 */
	public function insertID( $query ){
		return $query->insert_id;
	}
	
	
	/**
	 * Gibt die Anzahl an betroffenen Zeilen eines MySQL-Befehls zurück
	 * @param Object 'MySQLiQuery' $query
	 * @return int
	 */
	public function affectedRows( $query ){
		return (int) $query->affected_rows;
	}
	
	
	/**
	 * Gibt den aktuellen DATETIME String des Datenbankservers zurück
	 * @return String
	 */
	public function getSQLDateTime(){
		$resultRow = $this->resultRow("SELECT NOW() as datum");
		return $resultRow["datum"];
	}
	
	
	/**
	 * Gibt den Status von AutoCommit als Boolean zurück
	 * @return Boolean
	 */
	private function getAutoCommitState(){
		$resultRow = $this->resultRow("select @@autocommit state");
		$this->autocommit = (Boolean) $resultRow["state"];
		return $this->autocommit;
	}
	
	
	/**
	 * Festlegen des AutoCommitState (false startet eine Transaktion!)
	 * Diese Funktionalität ist nur mit Transaktionssicheren Datenbanken möglich (InnoDB, Falcon, etc.)!
	 * @param Boolean $bool
	 * @return void
	 */
	public function setAutoCommitState( $bool ){
		
		// Wenn keine Verbindung zur Datenbank besteht, versuche sie aufzubauen
		if( $this->dbCon == null ){
			$this->connect();
		}
		
		if( $this->dbCon != null ){		
			if( (Boolean) $bool == true ){
				@$this->dbCon->autocommit(true);
				$this->autocommit = true;
			}
			elseif( (Boolean) $bool == false ){
				$this->dbCon->autocommit(false);
				$this->autocommit = false;
			}
		}
		else{
			throw new SQLException("Unable to change autocommit state while not connected!");
		}
	}
	
	
	/**
	 * Gibt eine Liste gesammelter stiller Errors zurück
	 * @return String[]
	 */
	public function getErrors(){
		return $this->errors;
	}
	
	
	/**
	 * Versucht eine Logdatei mit einem Fehler zu schreiben
	 * @param String $error
	 * @return void
	 */
	private function logError( $error ){
		
		// Festlegen des Logpfades
		$logPath = ( $this->logPath != null ) ? realpath( $this->logPath ) : realpath( dirname(__FILE__) );
		
		// Versuche eine Logdatei im Klassenpfad zu erstellen
		if( is_writable($logPath) ){
			$datum = date("c");
			$open = @fopen($logPath."/class.Mysql.log","a");
			$write = @fwrite($open,"[{$datum}]: {$error}\r\n\r\n");
			$close = @fclose($open);
		}
	}
	
	
	/**
	 * Setzte den Logpath
	 * @param $logPath
	 * @return void
	 */
	public function setLogPath( $logPath ){
		$this->logPath = $this->clean($logPath);
	}

	
	
	/**
	 * Führe ein Vollbackup der Datenbank aus (WDF Version 1.1)
	 * @param String $mode="wdf"
	 * 		Gibt an, in welchem Dateiformat das Backup erstellt werden soll (Standard ist "wdf")
	 * 		Alternativ kann auch das unverschlüsselte und unkomprimierte "sql" Format gewählt werden
	 * @param String $cryptoKey
	 * 		Zum verschlüsseln zu verwendender alternativer Key (optional)
	 * @return String fileName
	 */
	public function backupDatabase( $mode = "sql", $cryptoKey = '' ){
		
		// Speicherlimit festlegen
		@ini_set("memory_limit","-1");
		@ini_set("max_execution_time", 300);
		
		(String) $sql = "";
		
		// Backup-Pfad
		(String) $path = $this->config["system_path"] . "/backup";
		(String) $tmpFileName = $this->database."_".time().".sql";
		(String) $tmpFile = $path ."/". $tmpFileName;

		// SQL-Header
		$sql .= "-- @CHARSET UTF-8" . "\r\n";
		$sql .= "-- Backup der Datenbank `{$this->database}`" . "\r\n";
		$sql .= "-- Datum: ". date("c")."\r\n";
		$sql .= "-- Server Version: ". $this->getVersion() ."\r\n\r\n";
		$sql .= "SET FOREIGN_KEY_CHECKS=0;" . "\r\n/*SPLIT*/\r\n";
		$sql .= "SET UNIQUE_CHECKS=0;" . "\r\n/*SPLIT*/\r\n";
		$sql .= "SET NAMES 'UTF8';" . "\r\n/*SPLIT*/\r\n";
		$sql .= "SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';" . "\r\n/*SPLIT*/\r\n";
		// Schreibe Daten in eine temporäre Datei, um den Arbeitsspeicher zu entlasten
		file_put_contents($tmpFile,$sql,FILE_APPEND | LOCK_EX);
		$sql = "";
		
		// Liste alle Tabellen in der Datenbank auf
		$query = $this->query("SET SQL_QUOTE_SHOW_CREATE=1;");	// Einfache Anführungszeichen abschalten
		$query = $this->query("SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';");
		$result = $this->result("SHOW TABLES FROM `{$this->database}`");
		
		// Durchlaufe die Liste aller Tabellen
		while( $row = $this->fetchAssoc($result) ){
		
			$sql = "";
			$tabelle = $row["Tables_in_{$this->database}"];
			
			// Storage-Engine auslesen
			$resEngine = $this->resultRow("SHOW TABLE STATUS WHERE Name = '{$tabelle}'");
			$storageEngine = $resEngine["Engine"];
		
			######## CREATE TABLE ##################################################################################################################
			// Auflisten des CREATE Scripts für die aktuelle Tabelle
			$fields = Array();
			$uks = Array(); // Liste mit UNIQUE Keys
			$nfd = Array(); // Liste mit NULL Feldern
			$ifd = Array(); // Liste mit integer Feldern
			
			$resCreate = $this->resultRow("SHOW CREATE TABLE `{$tabelle}`");
			$sql .= "DROP TABLE IF EXISTS `{$tabelle}`" . ";\r\n/*SPLIT*/\r\n";
			$sql .= $resCreate["Create Table"] . ";\r\n/*SPLIT*/\r\n";
			
			// Auslesen der Felder der aktuellen Tabelle
			$resCols = $this->result("SHOW COLUMNS FROM {$tabelle}");
			while( $rowCols = $this->fetchAssoc($resCols) ){
				$fields[] = "`". $rowCols["Field"] ."`";
				if( $rowCols["Null"] == "YES" ) $nfd[] = $rowCols["Field"];	// NULL Feld
				if( $rowCols["Key"] == "UNI" ) $uks[] = $rowCols["Field"];	// UNIQUE Feld
				if( strpos($rowCols["Type"],"int") !== false ) $ifd[] = $rowCols["Field"];	// Int Feld
			}			
			######## INSERT INTO ###################################################################################################################
			// Starte eine Transaktion, wenn es sich um InnoDB handelt
			if( $storageEngine == "InnoDB" ){
				$sql .= "START TRANSACTION;\r\n/*SPLIT*/\r\n";
			}
			
			// Schreibe Daten in eine temporäre Datei, um den Arbeitsspeicher zu entlasten
			file_put_contents($tmpFile,$sql,FILE_APPEND | LOCK_EX);
			$sql = "";
			
			// Lese die Anzahl an Einträgen aus der Tabelle aus
			$resCount = $this->resultRow("SELECT count(*) AS count FROM `{$tabelle}`");
			$itemCount = $resCount["count"];
			$itemPageSize = 1000; // Reihen pro Seite
			
			$k = 0;
			while( $k < $itemCount ){
				// Durchlaufe die Tabelle und sichere alle Datensätze
				$res = $this->result("SELECT * FROM `{$tabelle}` LIMIT {$k},{$itemPageSize}");				
				while( $data = $this->fetchAssoc($res) ){
					$i=0; // Schleifenzähler i auf 0 setzen
					$sql .= "INSERT INTO `{$tabelle}` (". implode(",",$fields) .") VALUES (";
				
					foreach( $data as $key => $value ){
						if( $i > 0 ) $sql .= ",";
						$sql .= (addslashes(trim($value)) == "" && in_array($key,$nfd) ) ? "NULL" :
						( in_array($key,$ifd) ? trim($value) : "'". addslashes(trim($value)) ."'" );
						$i++;
					}
					// Schreibe Daten in eine temporäre Datei, um den Arbeitsspeicher zu entlasten
					file_put_contents($tmpFile,$sql,FILE_APPEND | LOCK_EX);
					$sql = ");\r\n/*SPLIT*/\r\n";
				}
				$k+=$itemPageSize;
			}		

			######## INSERT INTO ENDE ##############################################################################################################
			
			// Commiten einer Transaktion, wenn es sich um InnoDB handelt
			if( $storageEngine == "InnoDB" ){
				$sql .= "COMMIT;\r\n/*SPLIT*/\r\n";
			}
			
			// Schreibe Daten in eine temporäre Datei, um den Arbeitsspeicher zu entlasten
			file_put_contents($tmpFile,$sql,FILE_APPEND | LOCK_EX);
			$sql = "";
		}

		// Modus abfragen
		// Bei "wdf" wird die Ausgabe nach der wdf Spezifikation behandelt
		// Dies benötigt zwar mehr Arbeitsspeicher, ist aber sicherer
		if( $mode == "wdf" ){
			// Lade die SQL-Befehle aus der temporären Datei
			$output = file_get_contents($tmpFile);
			// Ausgabe komprimieren
			$output = gzcompress(trim($output),9);
			// Ausgabe verschlüsseln
			$crypto = new Crypto($cryptoKey);
			$output = $crypto->encrypt($output,0);
			
			$fileName = $this->database."_".time().".wdf";
			file_put_contents($path."/".$fileName,"WDF_1.1 " . $output);
			@unlink($path ."/". $tmpFileName);
		}
		// Lasse die SQL-Datei vom Server unverschlüsselt komprimieren
		elseif( $mode == "gzip" && function_exists('shell_exec') && (int) ini_get('safe_mode') == 0 && !in_array('shell_exec',explode(",",ini_get('disable_functions'))) ){
			$exec = shell_exec("gzip -f -9 {$path}/{$tmpFileName}");
			$fileName = $tmpFileName;
		}
		// Alternativ kann die Datei als unverschlüsselte/unkomprimierte sql Datei abgelegt werden
		elseif( $mode == "sql" ){
			$fileName = $tmpFileName;
		}
		return $fileName;
	}
	
	
	/**
	 * Stelle eine Datenbank mit wieder her (WDF Version 1.1)
	 * @param String $fileName 
	 * 		Dateiname, der zu wiederstellenden Datei im Backup Verzeichnis
	 * @param String $cryptoKey
	 * 		Zum Entschlüsseln zu verwendender alternativer Key (optional)
	 * @return void
	 */
	public function restoreDatabase( $fileName, $cryptoKey = '' ){
		
		// Speicherlimit festlegen
		@ini_set("memory_limit","-1");
		@ini_set("max_execution_time", 300);
		
		// Backup-Pfad
		$path = $this->config["system_path"] . "/backup";
		
		// Laden der SQL-Datei als String
		$input = file_get_contents($path."/".$fileName);
		(Boolean) $unlink = true;
		
		// Dateityp ermitteln
		// WDF-Format behandeln
		if( substr($fileName,-4) == ".wdf" ){		
			// Nach WDF Flag suchen
			$pos = strpos($input,"WDF_");
			
			// WDF Version 1.0 enthält noch keine Versionsinformationen
			if( $pos !== false ){
				$wdf_version = trim(substr($input,0,7));
				$input = trim(substr($input,8));
			}
			else{
				$wdf_version = "WDF_1.0"; // Version 1.0 wird nicht länger unterstützt und daher wird hier abgebrochen
				throw new Exception("Unsupported or unknown wdf format: restore aborted.");
				return;
			}
	
			// Crypto zum entschlüsseln laden
			$crypto = new Crypto($cryptoKey);
			// String entschlüsseln
			$input = $crypto->decrypt($input,0);
			// String dekomprimieren
			$input = gzuncompress($input);

			// Temporäre Datei schreiben
			file_put_contents($path."/".$fileName.".sql", $input);
		}
		elseif( substr($fileName,-7) == ".sql.gz" ){
			// Temporäre Datei schreiben
			$open = gzopen($path."/".$fileName, "r");
			$content = gzread($open, filesize($path."/".$fileName));
			$close = gzclose($open);
			file_put_contents($path."/".$fileName.".sql", $content);
		}
		elseif( substr($fileName,-4) == ".sql" ) {
			// Wenn es sich um eine .sql Datei handelt, schneide die Dateiendung ab
			$fileName = substr($fileName,0,-4);
			$unlink = false;
		}
		else{
			throw new Exception("Unsupported file format: restore aborted.");
			return;
		}
		
		// Input aus dem Speicher entfernen
		unset($input);
		
		// Import durchführen
		// Versuche einen Shell-Befehl abzusenden, um den Import direkt über mysql auszuführen (performanter)
		if( function_exists('shell_exec') && (int) ini_get('safe_mode') == 0 && !in_array('shell_exec',explode(",",ini_get('disable_functions'))) ){
			$exec_command = "mysql -h {$this->host} -P {$this->port} -u {$this->username} ";
			if( $this->userpass != NULL ) $exec_command .= "-p{$this->userpass} ";
			if( $this->socket != NULL ) $exec_command .= "-S {$this->socket} ";
			$exec_command .= "{$this->database} < {$path}/{$fileName}.sql";
			$exec = shell_exec($exec_command);
		}
		// Alternativ wird die Datei mit PHP importiert
		else{
			$sql = explode("/*SPLIT*/",file_get_contents($path."/".$fileName.".sql"));
			
			// Führe alle SQL-Befehle nacheinander aus
			foreach( $sql as $value ){
				if( trim($value) != "" ){
					$this->query( trim($value) );
				}
			}
		}
		
		// Temporäre Datei entfernen
		if( $unlink ) @unlink($path."/".$fileName.".sql");
	}

	
	/**
	 * MySQL Destruktor
	 * Wird immer zuletzt ausgeführt
	 * @return void
	 */
	public function __destruct(){
			
		// Falls eine Datenbankverbindung besteht
		if( $this->isConnected ){

			// Committen der aktuellen Transaktion, sofern AutoCommit aus ist !
			if( $this->autocommit == false ){
				if( $this->dbCon->commit() == false ) {
					throw new SQLException("Could not commit transaction: ". print_r($this->query_exec,true));
				}
			}
			
			// DEBUG: Schreiben der ausgeführten Abfragen in eine Session-Variable
			if( $this->config["debug"] == true ){
				$class = ( $this->caller == null ) ? get_class($this) : $this->caller;
				$_SESSION["db"]["queryCount"] += $this->query_counter;
				
				(int) $i = 0;
				while( $_SESSION["db"]["queryExec"][$class][$i] != null ) $i++;
				
				$_SESSION["db"]["queryExec"][$class][$i] = $this->query_exec;				
				$_SESSION["db"]["isConnected"] = true;
			}
			
			// Trennen aller Results
			foreach( $this->results as $value ){
				@$value->close();
			}
			
			// Trennen der Datenbank-Verbindung
			$this->dbCon->close();
		}
	}
}



######################
/*********************
 * CUSTOM EXCEPTIONS *
 ********************/
######################

if( !class_exists('SQLException', false) ){
	class SQLException extends Exception {
		public function SQLException($message, $code = 0) {
	        parent::__construct($message, $code);
	    }
	    public function __toString() {
	        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
	    }
	}
}

if( !class_exists('SQLQueryException', false) ){
	class SQLQueryException extends Exception {
		public function SQLQueryException($message, $code = 0) {
	        parent::__construct($message, $code);
	    }
	    public function __toString() {
	        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
	    }
	}	
}

if( !class_exists('SQLResultException', false) ){
	class SQLResultException extends Exception {
		public function SQLResultException($message, $code = 0) {
	        parent::__construct($message, $code);
	    }
	    public function __toString() {
	        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
	    }	
	}
}
?>