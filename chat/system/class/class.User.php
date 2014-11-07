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

class User extends Chat {
	
	const USER_VERSION = 0802;
	public $user_name="", $hasPrivilege=false;
	
	
	/**
	 * Returns Users user_id
	 * @return int user_id
	 */
	public function getUserId(){
		return $_SESSION["user_id"];
	}
	
	
	/**
	 * Sets Users user_id
	 * @param int $user_id
	 * @return void
	 */
	public function setUserId( $user_id ){
		$_SESSION["user_id"] = $user_id;
	}
	
	
	/**
	 * Returns Users Username
	 * @param Boolean request
	 * @return String user_name
	 */
	public function getUserName( $request = false ){
		
		if($_SESSION["username"] != "" && $request == false ){
			return $_SESSION["username"];
		}
		else{		
			global $db;
			$result = $this->result("SELECT username FROM {$db["user"]} WHERE id = '{$this->getUserId()}'");
		
			while( $row = $result->fetch_assoc() ){
				$user_name = $row["username"];
				break;
			}			
			return $user_name;
		}		
	}

	
	/**
	 * Sets Users username
	 * @param String $username
	 * @return void
	 */
	public function setUserName( $username ){
		$_SESSION["username"] = $username;
	}
	
	
	/**
	 * Gets Users detailed data set
	 * @param String[] $username
	 */
	public function getUserDetails( $username ){
		global $db;
		$username = $this->clean($username);
		return $this->resultRow("SELECT * FROM {$db["user"]} u LEFT JOIN {$db["userdetails"]} d ON u.id=d.id LEFT JOIN {$db["groups"]} g ON u.level=g.level WHERE u.username = '{$username}'");
	}
	
	
	/**
	 * Returns Users Status
	 * @return int
	 */
	public function getUserStatus(){
		
		global $db;
		$result = $this->result("SELECT `status` FROM {$db["user"]} WHERE id = '{$this->getUserId()}'");
	
		while( $row = $result->fetch_assoc() ){
			return $row["status"];
		}
		return 0;		
	}
	
	
	/**
	 * Sets users status
	 * @param String $username
	 * @param int $status
	 * @return void
	 */
	public function setUserStatus( $username, $status ){		
		global $db;
		$query = $this->query("UPDATE {$db["user"]} SET `status` = '{$status}' WHERE username = '{$username}'");
	}
	
	
	/**
	 * Returns a boolean about the privilege access 
	 * @param String $p
	 * @return Boolean
	 */
	public function hasPrivilege( $p ){
		
		global $db;
		if( $p != null && $p != "" )
		{
			$result = $this->result("SELECT p.{$p} FROM {$db["online"]} o LEFT JOIN {$db["groups"]} g ON o.level=g.level LEFT JOIN shchat_privileges0 p ON g.group_id=p.group_id WHERE o.username = '{$this->getUserName()}'");
			
			while( $row = $result->fetch_assoc() ){
				
				if( $row[$p] == "0" || $row[$p] == 0 ){
					return false;
				}
				else{
					return true;
				}
			}
		}
		else{
			return false;
		}		
	}
	
	
	/**
	 * Returns a boolean, if the user is in channel
	 * @param String $username
	 * @return Boolean
	 */
	public function isInChannel( $username ){
		
		global $db;
		$channel = new Channel();
		$result = $this->result("SELECT id FROM {$db["online"]} WHERE username = '{$username}' AND channel = '{$channel->getChannelName()}'");
		
		while( $row = $result->fetch_assoc() ){
			return true;
		}
		return false;
	}
	
	
	/**
	 * Checks if a user exists
	 * @param String $username
	 * @return Boolean
	 */
	public function exists( $username ){
		
		global $db;
		$result = $this->result("SELECT username FROM {$db["user"]} WHERE LOWER(username) = LOWER('{$username}')");
		
		if( $result->num_rows == 1 ){
			return true;
		}
		return false;		
	}
	
	
	/**
	 * Returns a boolean, if the user is online
	 * @param String $username
	 * @return Boolean
	 */
	public function isOnline( $username ){
		
		global $db;
		$result = $this->result("SELECT id FROM {$db["online"]} WHERE username = '{$username}'");
		
		while( $row = $result->fetch_assoc() ){
			return true;
		}
		return false;
	}
	
	
	/**
	 * Returns a boolean, if the user is away, if no username is given, the current will be used
	 * @param String $username
	 * @return Boolean
	 */	
	public function isAway( $username = "" ){
		
		global $db;
		if( $username == "" ){
			$result = $this->result("SELECT away FROM {$db["user"]} WHERE username = '{$this->getUserName()}'");
		}
		else {
			$result = $this->result("SELECT away FROM {$db["user"]} WHERE username = '{$username}'");
		}
		
		while( $row = $result->fetch_assoc() ){
			if( $row["away"] != "" ){
				return true;
			}
		}
		return false;
	}
	
	
	/**
	 * Sets Users away message/status
	 * @param String $msg
	 * @return void
	 */	
	public function setAway( $msg ){
		
		global $db;
		$query = $this->query("UPDATE {$db["user"]} SET away = '{$msg}' WHERE id = '{$this->getUserId()}'");
	}
	
	
	/**
	 * Sets users level
	 * @param String $username
	 * @param int $level
	 * @return void
	 */
	public function setUserLevel( $username, $level ){
		
		global $db;
		$query = $this->query("UPDATE {$db["user"]} SET level = '{$level}' WHERE username = '{$username}'");
	}
	
	
	/**
	 * Sets users online level
	 * @param String $username
	 * @param int $level
	 * @return void
	 */
	public function setOnlineLevel( $username, $level ){
		
		global $db;
		$query = $this->query("UPDATE {$db["online"]} SET level = '{$level}' WHERE username = '{$username}'");
	}
	
	
	/**
	 * Returns a boolean, if the user is chatadmin
	 * @param String $username
	 * @return Boolean
	 */
	public function isChatAdmin( $username ){
		
		global $db;
		$result = $this->result("SELECT level FROM {$db["user"]} WHERE username = '{$username}'");

		while( $row = $result->fetch_assoc() ){
			if( $row["level"] == 999 || $row["level"] == "999" )
				return true;
			else{
				return false;
			}	
		}
		
		return false;
	}
	
	
	/**
	 * Sets the last login of a user to NOW
	 * @return void
	 */
	public function setLastLogin(){
		
		global $db;
		$_SESSION["last_login"] = time();
		$ip = $_SERVER["REMOTE_ADDR"];
		$hostname = gethostbyaddr($ip);
		$client = $_SERVER["HTTP_USER_AGENT"];
		$this->query("UPDATE {$db["user"]} SET `status` = '1', `last_login` = UNIX_TIMESTAMP(), `last_reload` = UNIX_TIMESTAMP(), ipadress = '{$ip}', hostname = '{$hostname}', client = '{$client}', `away` = '' WHERE `username` = '{$this->getUserName()}'");
	}
	
	
	/**
	 * User Logout
	 * @param String [$username=""]
	 * @return void
	 */
	public function logout( $username = "" ){
		
		global $db;		
		if( $username == "" ){
			$this->query("UPDATE {$db["user"]} SET `status` = '0', `away` = '' WHERE `id` = '{$this->getUserId()}'");	
		}
		else{
			$this->query("UPDATE {$db["user"]} SET `status` = '0', `away` = '' WHERE `username` = '{$username}'");
		}
	}
	
	
	/**
	 * Bans a user from the chat
	 * @param String [$username=""]
	 * @return void
	 */
	public function setBanned( $username = "" ){
		
		global $db;		
		if( $username == "" ){
			$this->query("UPDATE {$db["user"]} SET `status` = '2', `away` = '' WHERE `id` = '{$this->getUserId()}'");			
		}
		else{
			$this->query("UPDATE {$db["user"]} SET `status` = '2', `away` = '' WHERE `username` = '{$username}'");
		}
	}
	
	
	/**
	 * Checks the User data
	 * @param String $name
	 * @param String $pass
	 * @return integer
	 */
	public function checkUserLogin( $name, $pass ){
		
		global $db;
		$result = $this->result("SELECT id,status FROM {$db["user"]} WHERE LOWER(username) = LOWER('$name') AND userpass = MD5('$pass')");
		
		while( $row = $result->fetch_assoc() ){
			
			if( $row["status"] == "2" ){
				return 2;	// Benutzer ist verbannt
			}
			elseif( $row["status"] == "3" ){
				return 3;	// Benutzer ist inaktiv
			}
			else{
				$this->setUserId($row["id"]);
				$this->setUserName( $this->getUserName(true) );
				return 1;	// Benutzer wurde eingeloggt
			}
		}		
		return 0;	// Benutzer existiert nicht
	}
	
	
	/**
	 * Returns the User Level
	 * @return int
	 */
	public function getUserLevel(){
		
		global $db;
		$result = $this->result("SELECT level FROM {$db["user"]} WHERE id = '{$this->getUserId()}'");
	
		while( $row = $result->fetch_assoc() ){
			return (int)$row["level"];	
		}
		return 0;		
	}
	
	
	/**
	 * Returns the User Level
	 * @return void
	 */
	public function getUserChannelLevel(){
		
		global $db;
		$result = $this->result("SELECT level FROM {$db["online"]} WHERE username = '{$this->getUserName()}'");
	
		while( $row = $result->fetch_assoc() ){
			return (int)$row["level"];	
		}
		return 0;		
	}	
	
	
	/**
	 * Returns the User Color
	 * @return void
	 */
	public function getUserColor(){
		
		global $db;
		$result = $this->result("SELECT color FROM {$db["userdetails"]} WHERE id = '{$this->getUserId()}'");
	
		while( $row = $result->fetch_assoc() ){
			$user_color = $row["color"];
			break;
		}
		
		return (String)$user_color;	
	}
	
		
	/**
	 * Sets the Users color 
	 * @param String $color
	 * @return void
	 */
	public function setUserColor( $color ){
		
		global $db;
		$query = $this->query("UPDATE `{$db["userdetails"]}` SET `color` = '{$color}' WHERE `id` = '{$this->getUserId()}'");
	}
	
	
	/**
	 * Returns the User AutoScroll value
	 * @return void
	 */
	public function getUserAutoScroll(){
		
		global $db;
		$result = $this->result("SELECT autoscroll FROM {$db["userdetails"]} WHERE id = '{$this->getUserId()}'");
	
		while( $row = $result->fetch_assoc() ){
			$user_autoscroll = $row["autoscroll"];
			break;
		}
		
		return (int)$user_autoscroll;	
	}
	
	
	/**
	 * sets users autoscroll value
	 * @param int $user_id
	 * @param int $autoscroll
	 * @return void
	 */
	public function setUserAutoScroll( $user_id, $autoscroll ){
		
		global $db;
		$user_id = (int) $user_id;
		$autoscroll = (int) $autoscroll;
		$query = $this->query("UPDATE {$db["userdetails"]} SET autoscroll = '{$autoscroll}' WHERE id = '{$user_id}'");
	}
	
	
	/**
	 * Returns the Users E-Mail
	 * @return void
	 */
	public function getUserEmail(){
		
		global $db;
		$result = $this->result("SELECT email FROM {$db["userdetails"]} WHERE id = '{$this->getUserId()}'");
	
		while( $row = $result->fetch_assoc() ){
			$user_email = $row["email"];
			break;
		}
		
		return (String)$user_email;	
	}

	
	/**
	 * Returns the Users Last Login
	 * @return void
	 */
	public function getLastLogin(){
		
		if( $_SESSION["last_login"] != "" ){
			return $_SESSION["last_login"];
		}
		else{
			global $db;
			$result = $this->result("SELECT last_login FROM {$db["user"]} WHERE id = '{$this->getUserId()}'");
		
			while( $row = $result->fetch_assoc() ){
				$last_login = $row["last_login"];
				break;
			}
			
			return (String)$last_login;			
		}
	}
	
	
	/**
	 * Returns the channel name in which a user is online if he is online
	 * @param String $username
	 * @return String
	 */
	public function getUserChannelOnline( $username ){
		
		global $db;
		$result = $this->result("SELECT channel FROM {$db["online"]} WHERE username = '{$username}'");
		
		while( $row = $result->fetch_assoc() ){
			return $row["channel"];
		}
		return "";		
	}
	
	
	/**
	 * Returns users group name
	 * @param String $username
	 * @return String
	 */
	public function getGroupName( $username ){
		
		global $db;
		$result = $this->result("SELECT g.rang FROM {$db["groups"]} g LEFT JOIN {$db["user"]} u ON g.level=u.level WHERE u.username = '{$username}'");
		
		while( $row = $result->fetch_assoc() ){
			return $row["rang"];
		}
		return "";
	}
	
	
	/**
	 * Returns users group symbol
	 * @param int $level
	 * @return String
	 */
	public function getGroupSymbol( $level ){
		
		global $db;
		$result = $this->result("SELECT icon FROM {$db["groups"]} WHERE level = '{$level}'");
		
		while( $row = $result->fetch_assoc() ){
			if( $row["icon"] == "" ){
				$row["icon"] = "&#160;";
			}
			return $row["icon"];
		}
		return "";
	}
	
	
	/**
	 * Returns the users gender
	 * @param int $user_id
	 * @return String
	 */
	public function getUserGender( $user_id ){
		
		return $this->getCustomField("gender",$user_id);
	}

	
	/**
	 * Returns the Users Custom Field
	 * @param String $field
	 * @param int $user_id
	 * @return String
	 */
	public function getCustomField( $field, $user_id = 0 ){
		
		global $db;
		if( $user == 0 ){
			$result = $this->result("SELECT $field FROM {$db["userdetails"]} WHERE id = '{$this->getUserId()}'");
		}
		else {
			$result = $this->result("SELECT $field FROM {$db["userdetails"]} WHERE id = '{$user_id}'");
		}
	
		while( $row = $result->fetch_assoc() ){
			return $row[$field];	
		}
		return "";		
	}
	
	
	/**
	 * Returns an Object with user data
	 * @param int $user_id
	 * @return String[]
	 */
	public function getData( $user_id ){
		
		global $db;
		$result = $this->result("SELECT * FROM {$db["user"]} u LEFT JOIN {$db["userdetails"]} d ON u.id=d.id LEFT JOIN {$db["groups"]} g ON u.level=g.level WHERE u.id = '{$_REQUEST["id"]}'");
		$row = $result->fetch_assoc();
		
		return $row;		
	}
	
	
	/**
	 * Returns the users silence state
	 * @return int
	 */
	public function getUserSilence(){
		
		global $db;
		$result = $this->result("SELECT silent FROM {$db["user"]} WHERE id = '{$this->getUserId()}'");
	
		while( $row = $result->fetch_assoc() ){
			return (int)$row["silent"];
		}
		return 0;		
	}
	
	
	/**
	 * Checks if the user is silent
	 * @return Boolean
	 */
	public function isSilent(){
		
		if( $this->getUserSilence() >= time() ){
			return true;
		}
		return false;
	}
	
	
	/**
	 * Makes a user silent
	 * @param String $username
	 * @param int $duration
	 * @return void
	 */
	public function setSilence( $username, $duration ){
		
		global $db;
		$this->query("UPDATE {$db["user"]} SET silent = '{$duration}' WHERE username = '{$username}'");
	}
	
	
	/**
	 * Updates the users online status
	 * @return void
	 */
	public function updateOnlineStatus(){
		
		global $db;
		$time = time();
		$query = $this->query("UPDATE {$db["user"]} SET chat_time = chat_time+({$time}-last_reload), last_reload = '{$time}' WHERE id = '{$this->getUserId()}'");
	}
	
	
	/**
	 * Adds $count to the users char count
	 * @param int $count
	 * @return void
	 */
	public function updateCharCount( $count ){
		
		global $db;
		$query = $this->query("UPDATE {$db["user"]} SET charcount = charcount+{$count} WHERE id = '{$this->getUserId()}'");		
	}
	
	
	/**
	 * Edit userdetails to the current or selected user
	 * @param String $param
	 * @param String $value
	 * @param int $user_id
	 * @return int
	 */
	public function editUserDetail( $param, $value, $user_id = 0 ){
		
		global $db;
		$param = $this->clean($param);
		$value = $this->clean($value);
		$user_id = (int) (( $user_id != 0 ) ? $this->clean($user_id) : $_SESSION["user_id"]);
	
		if( $value == "@NULL" )
			$query = $this->query("UPDATE `{$db["userdetails"]}` SET {$param} = NULL WHERE id = '{$user_id}'");
		else
			$query = $this->query("UPDATE `{$db["userdetails"]}` SET {$param} = '{$value}' WHERE id = '{$user_id}'");
		return $this->affectedRows($query);
	}
	
	
	/**
	 * Deletes a user
	 * @param int $user_id
	 * @return Boolean
	 */
	public function delete( $user_id ){
		
		global $db;
		$result = $this->result("SELECT id,username FROM {$db["user"]} WHERE `id` = '{$user_id}' AND `level` != '999'");
		
		if( $result->num_rows == 1 ){
			$row = $result->fetch_assoc();
			$query = $this->query("DELETE FROM {$db["online"]} WHERE username = '{$row["username"]}'");
			$query = $this->query("DELETE FROM {$db["postings"]} WHERE username = '{$row["username"]}'");
			$query = $this->query("DELETE FROM {$db["userdetails"]} WHERE id = '{$row["id"]}'");
			$query = $this->query("DELETE FROM {$db["user"]} WHERE id = '{$row["id"]}'");
			return true;			
		}
		else{
			return false;
		}	
	}
}
?>