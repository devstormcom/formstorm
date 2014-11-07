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

class Logfile {
	
	const LOGFILE_VERSION = 0500;
	
	
	/**
	 * Adds a Logfile Message
	 * @param $file
	 * @param $msg
	 * @return void
	 */
	public function add( $file, $msg ){
		
		global $config;
		$open = fopen($config["system_path"] ."/log/" . $file,"a");
		$write = fwrite($open,"<". date("d.m.Y H:i:s") ."> ".trim($msg) . "\r\n");
		$close = fclose($open);
	}
}

?>