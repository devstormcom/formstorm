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

class Main {

	const MAIN_VERSION = 0402;
	
	/**
	 * Läd ein Template in die aktuelle Seite
	 * @param $tpl
	 * @return Boolean
	 */
	public function getTemplate( $tpl ){
		
		global $config,$db;
		$tpl = trim($tpl);
		
		if( file_exists( $config["include_path"] . "/" . $tpl . ".inc.php") ){
			include_once( $config["include_path"] . "/" . $tpl . ".inc.php" );
			return true;
		}
		return false;		
	}
}
?>