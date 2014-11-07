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

class Captcha
{
	const CAPTCHA_VERSION = 0403;
	
	
	/**
	 * Generiert einen Random-String
	 * @param $len Integer String-Length
	 * @return String
	 */
	public function randomString($len)
	{ 		
		//Der String $possible enthält alle Zeichen, die verwendet werden sollen 
		$possible="ABCDEFGHJKLMNPRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789"; 
		$str=""; 
		
		while( mb_strlen($str) < $len ) { 
		  $str.=substr($possible,(rand()%(strlen($possible))),1); 
		}
		 
		return $str; 
	}
}

?>