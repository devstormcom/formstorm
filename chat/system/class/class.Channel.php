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

class Channel extends Chat {
	
	const CHANNEL_VERSION = 1003;
	public $channel_name = "";
	
	
	/**
	 * Returns Channel ID of current channel
	 * @param String [$channel=""]
	 * @return int $channel_id
	 */
	public function getChannelId( $channel = "" ){
		
		global $db;
		if( $channel == "" ){
			return (int)$_SESSION["channel_id"];
		}
		else{
			$result = $this->result("SELECT id FROM {$db["channel"]} WHERE channel = '{$channel}' LIMIT 1");
			
			while( $row = $result->fetch_assoc() ){
				return $row["id"];
			}
		}
	}
	
	
	/**
	 * Sets Channel ID of current channel
	 * @param int $channel_id
	 * @return void
	 */
	public function setChannelId( $channel_id ){
		$_SESSION["channel_id"] = $channel_id;
		$_SESSION["channel_name"] = $this->getChannelName($channel_id);
	}
	
	
	/**
	 * Returns Name of current or given channel
	 * @param int [$channel_id=0]
	 *		0 = current channel
	 * @return String $channel_name
	 */
	public function getChannelName( $channel_id = 0 ){
		
		if( $_SESSION["channel_name"] != "" && $channel_id == 0 ){
			return $_SESSION["channel_name"];
		}
		else{
			global $db;
			$channel_name = "Channel {$channel_id} does not exist.";
			
			if( $channel_id == 0 ){
				$result = $this->result("SELECT channel FROM {$db["channel"]} WHERE id = '{$this->getChannelId()}' LIMIT 1");
			}
			else{
				$result = $this->result("SELECT channel FROM {$db["channel"]} WHERE id = '{$channel_id}' LIMIT 1");
			}
			
			while( $row = $result->fetch_assoc() ){
				$channel_name = $row["channel"];
				break;
			}
			
			return (String)$channel_name;
		}
	}
	
	
	/**
	 * Returns a boolean if the channel exists
	 * @param String $channel 
	 * 		channel name or channel_id 
	 * @return Boolean
	 */
	public function channelExists( $channel ){
		
		global $db;
		$result = $this->result("SELECT id FROM {$db["channel"]} WHERE channel = '{$channel}' OR id = '{$channel}'");
	
		if( $result->num_rows > 0 ){
			return true;
		}
		return false;
	}
	
	
	/**
	 * Adds a User to the Channel
	 * @param int $username
	 * @param int $level
	 * @return void
	 */
	public function addUser( $username, $level ){
		
		global $db;
		(int)$channel_id = $this->getChannelId();
		(String)$channel = $this->getChannelName( $channel_id );
		(String) $username = trim($username);
		(String)$sql = "";		
		
		$query = $this->query("DELETE FROM {$db["online"]} WHERE username = '{$username}'");
		$query = $this->query("INSERT INTO {$db["online"]} SET username = '{$username}', channel = '{$channel}', level = '{$level}'");
	}
	
	
	/**
	 * Adds a new hidden seperate channel
	 * @param String $channel_name
	 * @return void
	 */
	public function addSeparee( $channel_name ){
		
		global $db;
		$query = $this->query("INSERT INTO {$db["channel"]} VALUES ('', '{$channel_name}', 0, 0, 1, 1, 'Willkommen im Separee! Dieser Channel ist versteckt und wird geschlossen, sobald er leer ist.')");
	}
	
	
	/**
	 * Removes a User from the Channel
	 * @param String $username
	 * @return void
	 */
	public function removeUser( $username ){
		
		global $db;
		(String)$sql = "";
		
		$sql = "DELETE FROM {$db["online"]} WHERE username = '{$username}'";
		$this->query( $sql );
	}
	
	
	/**
	 * Checks if the channel is joinable by the current user
	 * @param int $channel_id
	 * @param int $userlevel
	 * @return Boolean
	 */
	public function isJoinable( $channel_id, $userlevel ){
		
		$minlevel = $this->getMinLevel( $channel_id );
		
		if( $minlevel > $userlevel ){
			return false;
		}
		else{
			return true;
		}		
	}
	
	
	/**
	 * Checks if the channel is full
	 * @param int $channel_id
	 * @return Boolean
	 */		
	public function isFull( $channel_id ){
		
		$online = $this->getOnlineUsers($channel_id);
		$limit = $this->getUserLimit($channel_id);
		
		if( $limit > 0 && $online >= $limit ){
			return true;
		}
		return false;
	}
	
	
	/**
	 * If the Channel is moderated or not
	 * @param int $channel_id
	 * @return Boolean
	 */
	public function isModerated( $channel_id = 0 ){
		
		global $db;
		(int)$channel_id = ( $channel_id == 0 )	?	$this->getChannelId()	:	$channel_id;
		$result = $this->result("SELECT moderated FROM {$db["channel"]} WHERE id = '$channel_id'");
		
		while( $row = $result->fetch_assoc() ){			
			if( $row["moderated"] == 1 || $row["moderated"] == "1" ){
				return true;
			}
			else{
				return false;
			}
		}
		return false;
	}
	
	
	/**
	 * Sets the channels moderated flag
	 * @param String $channel
	 * @param int $moderated
	 * @return void
	 */
	public function setModerated( $channel, $moderated ){
		
		global $db;
		$query = $this->query("UPDATE {$db["channel"]} SET moderated = '{$moderated}' WHERE channel = '{$channel}'");
	}

	
	/**
	 * If the Channel is hidden or not
	 * @param int $channel_id
	 * @return Boolean
	 */
	public function isHidden( $channel_id = 0 ){
		
		global $db;
		(int)$channel_id = ( $channel_id == 0 )	?	$this->getChannelId()	:	$channel_id;
		$result = $this->result("SELECT hidden FROM {$db["channel"]} WHERE id = '$channel_id'");
		
		while( $row = $result->fetch_assoc() ){			
			if( $row["hidden"] == 1 || $row["hidden"] == "1" ){
				return true;
			}
			else{
				return false;
			}
		}
		return false;
	}
	
	
	/**
	 * Sets the channels hidden flag
	 * @param String $channel
	 * @param int $hidden
	 * @return void
	 */
	public function setHidden( $channel, $hidden ){
		
		global $db;
		$query = $this->query("UPDATE {$db["channel"]} SET hidden = '{$hidden}' WHERE channel = '{$channel}'");
	}
	
	
	/**
	 * Returns the minlevel of the current or given channel
	 * @param int $channel_id
	 * @return Boolean
	 */
	public function getMinLevel( $channel_id = 0 ){
		
		global $db;
		(int)$channel_id = ( $channel_id == 0 )	?	$this->getChannelId()	:	$channel_id;
		$result = $this->result("SELECT minlevel FROM {$db["channel"]} WHERE id = '$channel_id'");
		
		while( $row = $result->fetch_assoc() ){			
			return $row["minlevel"];
		}
		return 0;
	}
	
	
	/**
	 * Sets the channels minlevel flag
	 * @param String $channel
	 * @param int $minlevel
	 * @return void
	 */
	public function setMinLevel( $channel, $minlevel ){
		
		global $db;
		$query = $this->query("UPDATE {$db["channel"]} SET minlevel = '{$minlevel}' WHERE channel = '{$channel}'");
	}
	
	
	/**
	 * Returns the Userlimit of the current or given channel
	 * @param int $channel_id
	 * @return int
	 */
	public function getUserLimit( $channel_id = 0 ){
		
		global $db;
		
		if( $channel_id == 0 ){
			$result = $this->result("SELECT userlimit FROM {$db["channel"]} WHERE channel = '{$this->getChannelName()}'");
		}else{
			$result = $this->result("SELECT userlimit FROM {$db["channel"]} WHERE id = '{$channel_id}'");
		}
		
		while( $row = $result->fetch_assoc() ){			
			return (int)$row["userlimit"];
		}
		return 0;
	}
	
	
	/**
	 * Sets the channels userlimit
	 * @param String $channel
	 * @param int $limit
	 * @return void
	 */
	public function setUserLimit( $channel, $limit ){
		
		global $db;
		$query = $this->query("UPDATE {$db["channel"]} SET userlimit = '{$limit}' WHERE channel = '{$channel}'");
	}
	
	
	/**
	 * Returns the Welcome message of the current or given channel
	 * @param int $channel_id
	 * @return String
	 */
	public function getWelcome( $channel_id = 0 ){
		
		global $db;
		(int)$channel_id = ( $channel_id == 0 )	?	$this->getChannelId()	:	$channel_id;
		$result = $this->result("SELECT welcome FROM {$db["channel"]} WHERE id = '$channel_id'");
		
		while( $row = $result->fetch_assoc() ){			
			return $row["welcome"];
		}
		return "";
	}
	
	
	/**
	 * Returns the Online Users in this channel
	 * @param int $channel_id
	 * @return int
	 */
	public function getOnlineUsers( $channel_id = 0 ){
		
		global $db;
		if( $channel_id == 0 ){
			$result = $this->result("SELECT count(*) as anzahl FROM {$db["online"]} WHERE channel = '{$this->getChannelName()}' LIMIT 1");
		} else {
			$result = $this->result("SELECT count(*) as anzahl FROM {$db["online"]} o LEFT JOIN {$db["channel"]} c ON o.channel=c.channel WHERE c.id = '{$channel_id}' LIMIT 1");
		}
		
		$row = $result->fetch_assoc();

		return (int)$row["anzahl"];
	}
	
	
	/**
	 * Returns the Online Userlist of a channel
	 * @param String $channel
	 * @return String
	 */
	public function getOnlineUserList( $channel ){
		
		global $db,$config;
		$user = new User();
		
		$output = "";
		$channel_name = "";
		$userlist = "";
		$i=0;
		
		if( $this->isModerated() ){ 
			$channel_name .= "+ "; 
		}
		if( $this->isHidden() ){
			$channel_name .= ".";
		}
		$channel_name .= "<b>{$this->getChannelName()}</b>";
		
		$result = $this->result("SELECT u.id,d.gender,o.* FROM {$db["user"]} u LEFT JOIN {$db["online"]} o ON u.username=o.username LEFT JOIN {$db["userdetails"]} d ON u.id=d.id WHERE `channel` = '{$this->getChannelName()}' ORDER by `level` DESC,`username`");
		
		while( $row = $result->fetch_assoc() ){

			$userlist .= "<div class=\"online-userlist". $i%2 ."\">";
			
			if( $row["username"] != "Chatbot" && $row["username"] != $user->getUserName() ){
				$userlist .= "<a href=\"javascript:whisper('{$row["username"]}');\"><img src='templates/{$config["template_name"]}/img/msg.gif' alt='whisper' border='0' /></a>";
			}
			else{
				$userlist .= "<img src='templates/{$config["template_name"]}/img/msg1.gif' alt='whisper' border='0' />";
			}
			
				
			if( $row["gender"] == "f" ){
				$userlist .= " <img src='templates/{$config["template_name"]}/img/female.gif' alt='Frau' /> ";
			}
			elseif( $row["gender"] == "m" ){
				$userlist .= " <img src='templates/{$config["template_name"]}/img/male.gif' alt='Mann' /> ";
			}
		
			if( $user->isAway($row["username"]) ){
				$userlist .= "<i>";
			}
			
			if( $user->exists($row["username"]) ){
				$userlist .= " <span style=\"font-size:10px;\">{$user->getGroupSymbol($row["level"])}</span> <a href=\"javascript:whois('{$row["username"]}');\">{$row["username"]}</a>";
			}
			else{
				$userlist .= " <span style=\"font-size:10px;\"><span>{$user->getGroupSymbol($row["level"])}</span> ".$row["username"];
			}
		
			if( $user->isAway($row["username"]) ){
				$userlist .= "</i>";
			}
		
			$userlist .= "</div><span />" . "\r\n";
			$i++;
		}
		
		if( $this->getUserLimit() == 0 ){
			$output = "<div><div id=\"online-channel\">{$channel_name} <span id=\"online-users\">({$this->getOnlineUsers($this->getChannelName())})</span></div>{$userlist}</div>";
		}
		else{
			$output = "<div><div id=\"online-channel\">{$channel_name} <span id=\"online-users\">({$this->getOnlineUsers($this->getChannelName())}/{$this->getUserLimit()})</span></div>{$userlist}</div>";
		}
		
		return $output;
	}
	
	
	/**
	 * Returns an object of channels
	 * @param int [$level=1]
	 * @param Boolean [$limited=true]
	 * @return String[] $channel_list
	 */
	public function getChannelList( $level = 1, $limited = true ){
		
		global $db;
		if( $limited ){
			$result = $this->result("SELECT id,channel FROM {$db["channel"]} WHERE hidden = '0' and minlevel <= '{$level}'");
		}
		else{
			$result = $this->result("SELECT id,channel FROM {$db["channel"]} WHERE hidden = '0' and minlevel <= '{$level}' AND userlimit = '0'");
		}
		
		$channel_list = array();
		
		while( $row = $result->fetch_assoc() ){			
			$channel_list[$row["id"]] = $row["channel"];
		}
		return $channel_list;
	}
	
	
	/**
	 * Returns the Channels as Option Fields
	 * @param int [$level=1]
	 * @return String $channel_list
	 */
	public function getChannelListOutput( $level = 1 ){
		
		global $db;
		$channel_list = "";
		foreach( $this->getChannelList($level,true) as $key => $value ){
					
			$channel_list .= "\r\n\t\t\t\t";
			$channel_list .= "<option value=\"{$key}\">{$value}</option>";
		}
		return $channel_list;
	}
	
	
	/**
	 * Returns Channel Content
	 * @return String
	 */
	public function getContent(){
		
		global $config,$db;
		$user = new User();
		$username = $user->getUserName();
		$output = "";
		$arr = array();
		
		if( $user->getUserStatus() == 1 )
		{		
			if( !$config["old_lines"] ){
				$result = $this->result("SELECT * FROM {$db["postings"]} WHERE `time` >= {$user->getLastLogin()}+1 AND ( `channel` = '{$this->getChannelName()}' OR ( `channel` = 'WHISPER' AND `whisperto` = '{$username}' ) OR ( `channel` = 'WHISPER' AND `username` = '{$username}' ) OR ( `channel` = 'SYSTEM' AND `whisperto` = '{$username}' ) OR `channel` = 'BROADCAST' ) ORDER BY id DESC LIMIT 50");
			}
			else{
				$gestern = strtotime( date("Y-m-d H:i:s",strtotime("-24 hours")) );
				$result = $this->result("SELECT * FROM {$db["postings"]} WHERE `time` >= '{$gestern}' AND ( `channel` = '{$this->getChannelName()}' OR ( `channel` = 'WHISPER' AND `whisperto` = '{$username}' ) OR ( `channel` = 'WHISPER' AND `username` = '{$username}' ) OR ( `channel` = 'SYSTEM' AND `whisperto` = '{$username}' ) OR `channel` = 'BROADCAST' ) ORDER BY id DESC LIMIT 50");
			}
			
			while( $row = $result->fetch_assoc() )
			{
				$time = date("H:i:s", $row["time"]);
				$output = "";

				//$row["message"] = html_entity_decode($row["message"],ENT_QUOTES,"UTF-8");
				$row["message"] = str_replace("%u20AC","€",$row["message"]);
		
				$output .= "<div class='text". $i%2 ."'>";
		
				if( $row["channel"] != "WHISPER" && $row["channel"] != "SYSTEM" && $row["channel"] != "BROADCAST" )
				{
					$output .= "<span class=\"content-time\">{$time}</span> <span style=\"color:{$row["color"]}\"><b>";
					if( $row["username"] != "Chatbot" && $row["username"] != $username ) $output .= "<a href=\"javascript:whisper('{$row["username"]}');\" style=\"color:{$row["color"]}\">";
					$output .= $row["username"];
					if( $row["username"] != "Chatbot" && $row["username"] != $username ) $output .= "</a>";
					$output .= "</b>: ". $row["message"] . "</span><br />\r\n";
				}		
				elseif( $row["channel"] == "WHISPER" && $row["whisperto"] == $username && $row["username"] != "Chatbot" ){
					$output .= "<span class=\"content-time\">{$time}</span> <span color=\"{$row["color"]}\"><i><b><a href=\"javascript:whisper('{$row["username"]}');\" style=\"color: {$row["color"]}\">{$row["username"]}</a></b> flüstert: {$row["message"]}</i></span><br />\r\n";
				}		
				elseif( $row["channel"] == "WHISPER" && $row["whisperto"] == $username && $row["username"] == "Chatbot" ){
					$output .= "<span class=\"content-time\">{$time}</span> <span color=\"{$row["color"]}\"><i><b>{$row["username"]}</b> flüstert: ". $row["message"] . "</i></span><br />\r\n";
				}		
				elseif( $row["channel"] == "WHISPER" && $row["username"] == $username ){
					$output .= "<span class=\"content-time\">{$time}</span> <span color=\"{$row["color"]}\">Ihr flüstert <b><a href=\"javascript:whisper('{$row["whisperto"]}');\"> {$row["whisperto"]}</a></b>: <i>{$row["message"]}</i></span><br />\r\n";
				}		
				elseif( $row["channel"] == "SYSTEM" && $row["whisperto"] == $username ){
					$output .= "<span class=\"content-time\">{$time}</span> <span><i><b>SYSTEM:</b> {$row["message"]}</i></span><br />\r\n";
				}		
				elseif( $row["channel"] == "BROADCAST" ){
					$output .= "<span class=\"content-time\">{$time}</span> <span><i>Broadcast von <b>{$row["username"]}</b>: {$row["message"]}</font></i></span><br />\r\n";
				}
		
				$output .= "</div>";
				$i++;
				
				$arr[] = $output;
			}
			
			$output .= "<span id=\"bottom\">&#160;</span>";
		}
		else {
			$output .= "<span><b>SESSION ABGELAUFEN!</b></span><br />Mögliche Ursachen: Sie wurden aus dem Chat gekickt oder waren zu lange inaktiv.";
			$output .= "<p><a href=\"index.php\" target=\"_self\">Zur Startseite wechseln</a></p>";
			unset($_SESSION["user_id"]);
			$arr[] = $output;
		}
		
		krsort($arr);
		$output = implode($arr);
		return (String)$output;	
	}
	
	
	/**
	 * Updates the online users list
	 * @return void
	 */
	public function updateOnlineUsers(){
		
		global $db;
		$chatinput = new Chatinput();
		$user = new User();
		$check_time = time() - 60;
		$result = $user->result("SELECT username FROM {$db["user"]} WHERE `last_reload` <= '{$check_time}' AND `status` = '1'");
		
		while( $row = $result->fetch_assoc() ){
			
			// Getrennte Benutzer löschen
			$query = $user->query("DELETE FROM {$db["online"]} WHERE `username` = '{$row["username"]}'");
			$query = $user->query("UPDATE {$db["user"]} SET `status` = '0' WHERE `username` = '{$row["username"]}'");
			$chatinput->addChatMsg("<i>Die Verbindung zu <b>{$row["username"]}</b> wurde unterbrochen!</i>",$user->getUserChannelOnline($row["username"]),"Chatbot","#000000");
		}
		
		// Lösche leere temporäre Channel
		$result = $user->result("SELECT c.channel,count(o.id) as anzahl FROM {$db["channel"]} c LEFT JOIN {$db["online"]} o ON c.channel=o.channel WHERE c.hidden = '1' AND c.minlevel = '1' GROUP by o.id HAVING anzahl = '0'");
		while( $row = $result->fetch_assoc() ){
			$query = $user->query("DELETE FROM {$db["channel"]} WHERE channel = '{$row["channel"]}'");
		}
	}
	
	
	/**
	 * Adds the Chatbot to the channel
	 * @param String $channel
	 * @return void
	 */
	public function addChatbot( $channel ){
		
		global $db;
		$query = $this->query("INSERT INTO {$db["online"]} (`username`, `channel`, `level`) VALUES ('Chatbot', '{$channel}', '999')");
	}
	
	
	/**
	 * Removes the Chatbot from a channel
	 * @param String $channel
	 * @return void
	 */
	public function removeChatbot( $channel ){

		global $db;
		$query = $this->query("DELETE FROM {$db["online"]} WHERE channel = '{$channel}' AND username = 'Chatbot'");
	}
}
?>