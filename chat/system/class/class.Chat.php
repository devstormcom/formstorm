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

class Chat extends Mysql {
	
	const CHAT_VERSION = 1003;
	
	/**
	 * Returns a String with all smileys 
	 * @return String
	 */
	public function getChatColors( $usercolor = "#000000", $lang = "" ){
		
		global $config;
		$lang = ( $lang == "" ) ? $config["language"] : $lang;
		$color_ini = $config["include_path"] . "/lang/{$lang}/chatcolors.ini";
		$colors = "";
		
		$open = file($color_ini);
		
		foreach( $open as $value ){
			
			$get = explode("|",$value);
			$color_name = trim($get[0]);
			$color_code = trim($get[1]);

			$colors .= "<option";
			if( $usercolor == $color_code ) $colors .= " selected=\"selected\"";
			$colors .= " value=\"{$color_code}\" style=\"color: {$color_code}\">{$color_name}</option>" . "\r\n";
		}
		return $colors;
	}
	
	
	/**
	 * Returns a String with all smileys
	 * @param int $limit 
	 * @return String
	 */
	public function getSmileys( $limit = 0 ){
		
		global $config;
		$smiley_ini = $config["basepath"] . "/system/smileys/smileys.ini";
		$smiley_uri = $config["scriptpath"] . "/system/smileys/";
		$output = "";
		$i=0;
		
		$open = file($smiley_ini);
		
		foreach( $open as $value ){
			
			$get = explode("|",$value);
			$filename = trim($get[0]);
			$command = trim($get[1]);

			$output .= "<a href=\"javascript:setsmiley('{$command}');\"><img src=\"{$smiley_uri}{$filename}\" border=\"0\" alt=\"smiley\" /></a> " . "\r\n";
			$i++;
			
			if( $limit > 0 && $i >= $limit ){
				break;
			}
		}
		return $output;
	}
	
	
	/**
	 * Converts a string into smileys
	 * @param $string
	 * @return String
	 */
	public function convertSmileys( $string ){
		
		global $config;
		$smiley_ini = $config["basepath"] . "/system/smileys/smileys.ini";
		$smiley_uri = "system/smileys/";

		$open = file($smiley_ini);
		
		foreach( $open as $value ){
			
			$get = explode("|",$value);
			$count = count($get);
			$filename = trim($get[0]);
			
			for($i=1; $i < $count; $i++){
				$string = str_replace($get[$i],"<img src=\"{$smiley_uri}{$filename}\" alt=\"Smiley\" />",$string);				
			}
		}
		return $string;
	}
	
	
	/**
	 * Converts an uri string into an url
	 * @param $string
	 * @return String
	 */
	public function convertUrl( $string ){

		$get = explode(" ",$string);

		for($i=0; $i<count($get);$i++ ){
			
			// HTTP
			if( mb_substr($get[$i],0,7) == "http://" ){
				$url = ( mb_strlen($get[$i]) > 50 ) ? mb_substr($get[$i],0,40) . "...". mb_substr($get[$i],-10) : $get[$i];
				$get[$i] = "<a href=\"{$get[$i]}\" target=\"_blank\">{$url}</a>";
			}
			// www/HTTP
			elseif( mb_substr($get[$i],0,4) == "www." ){
				$url = ( mb_strlen($get[$i]) > 50 ) ? mb_substr($get[$i],0,40) . "...". mb_substr($get[$i],-10) : $get[$i];
				$get[$i] = "<a href=\"http://{$get[$i]}\" target=\"_blank\">{$url}</a>";
			}
			// HTTPS
			elseif( mb_substr($get[$i],0,8) == "https://" ){
				$url = ( mb_strlen($get[$i]) > 50 ) ? mb_substr($get[$i],0,40) . "...". mb_substr($get[$i],-10) : $get[$i];
				$get[$i] = "<a href=\"{$get[$i]}\" target=\"_blank\">{$url}</a>";
			}
			// FTP
			elseif( mb_substr($get[$i],0,6) == "ftp://" ){
				$url = ( mb_strlen($get[$i]) > 50 ) ? mb_substr($get[$i],0,40) . "...". mb_substr($get[$i],-10) : $get[$i];
				$get[$i] = "<a href=\"{$get[$i]}\" target=\"_blank\">{$url}</a>";
			}				
		}
		return implode(" ",$get);		
	}
	
	
	/**
	 * Returns a language object
	 * @param String $lang
	 * @return String[]
	 */
	public function getLanguage( $lang = "" ){
		
		global $config;
		
		$lang = ( $lang == "" ) ? $config["language"] : $lang;
		$res = @parse_ini_file("include/".$config["template_name"] . "/lang/{$lang}/global.ini",true);
		
		if($res != null){
			foreach( $res as $key => $value ){
				foreach( $value as $key0 => $value0 ){
						$res[$key][$key0] = str_replace("{overall_title}",$config["overall_title"],$res[$key][$key0]);
				}
			}
			return $res;		
		}
		else{
			return "404: Ini File not Found";
		}		
	}
	
	
	/**
	 * Returns the Online Users in the Chat
	 * @return int
	 */
	public function getAllOnlineUsers(){
		
		global $db;
		$result = $this->result("SELECT count(*) as anzahl FROM {$db["online"]} LIMIT 1");
		$row = $result->fetch_assoc();

		return (int)$row["anzahl"];
	}
}
?>