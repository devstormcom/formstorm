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

class Chatinput extends Channel {

	const CHATINPUT_VERSION = 1000;
	
	/**
	 * Runs a Chat Command in the current channel
	 * @param String $input
	 * @return void
	 */
	public function runChatCommand( $input ){
		
		global $db;
		$user = new User();
		
		// Input-Filter
		$input = $this->clean($input,true,true);
		$strlen = mb_strlen( $input );
		
		// Chat-Command
		if( mb_substr($input,0,1) == "/" ){
			//########### USER-Commands ###########//		
			// Whisper
			if( ( mb_substr($input,0,3)=="/w " ) && $user->getUserStatus() == 1 && $user->hasPrivilege("WHISPER"))
			{
				if( preg_match("#\<(.*?)\>#", html_entity_decode($input), $matches) ){
					$input = str_replace("&#60;".$matches[1]."&#62;","",$input);
					$input = mb_substr($input,4);
					$whisperto = $matches[1];
				}
				else{
					$text = explode(" ",$input);
					$input = mb_substr($input,mb_strlen($text[0])+mb_strlen($text[1])+2);
					$whisperto = $text[1];
				}				
		
				if( $user->exists($whisperto) && $user->isOnline($whisperto) )
				{
					$this->addPrivateMsg($input,$whisperto);
					$user->updateCharCount($strlen);
				}		
				else{
					$this->addErrorMsg("Der Benutzer <b>{$whisperto}</b> wurde nicht gefunden!");
				}
			}
		
			// Join
			elseif( mb_substr($input,0,6)=="/join " && $user->getUserStatus() == 1 && $user->hasPrivilege("JOIN"))
			{
				$text = explode(" ",$input,2);
				$newchannel = $user->clean( mb_substr($input,mb_strlen($text[0])+1) );
				$isFull = $this->isFull( $this->getChannelId($newchannel) );
				$user_level = $user->getUserLevel();
				$user_name = $user->getUserName();
		
				if( $this->channelExists( $this->getChannelId($newchannel) ) && $user_level >= $this->getMinLevel() )
				{					
					if( $isFull && !$user->isChatAdmin($user_name)){
						$this->addErrorMsg("Der Channel <b>". $newchannel . "</b> ist voll!");
					}			
					else{
						$oldchannel = $this->getChannelName();
					
						if( $newchannel != $oldchannel && $this->isJoinable($this->getChannelId(),$user_level) )
						{
							$user->setLastLogin();
							$this->addSystemMsg("<i><b>{$user_name}</b> verlässt den Channel!</i>");	// Nachricht in den alten Channel senden
							$this->setChannelId( $this->getChannelId($newchannel) );	// Aktuellen Channel auf ID setzen

							$this->addUser( $user_name, $user_level );	// User dem Channel hinzufügen
							$this->addSystemMsg("<i><b>{$user_name}</b> betritt den Channel!</i>",$newchannel);	// Nachricht in den neuen Channel senden
						}
					}
				
				}			
				else{
					$this->addErrorMsg("Der Channel <b>". $newchannel . "</b> wurde nicht gefunden!");
				}
			}
		
			// Autoscroll
			elseif( substr($input,0,11)=="/autoscroll" && $user->getUserStatus() == 1 )
			{
				if(!$user->getUserAutoScroll()){
					$user->setUserAutoScroll($user->getUserId(), 1);
				}
				else {
					$user->setUserAutoScroll($user->getUserId(), 0);
				}
			}
		
			// SEP
			elseif(substr($input,0,5)=="/sep " && $user->getUserStatus() == 1 && $user->hasPrivilege("SEP"))
			{
				$text = explode(" ",$input,2);
		
				if(trim($text[1])){
					
					$sep = $user->clean($text[1]);										
					$newchannel = $user->clean($sep);
					$oldchannel = $user->clean($this->getChannelName());
					$channel_exists = $this->channelExists( $newchannel );
				
					//if( $newchannel != $oldchannel && $user->getUserStatus() == 1 && $this->isJoinable($this->getChannelId(),$user->getUserLevel()) && ($this->getOnlineUsers( $this->getChannelId($newchannel) ) < $this->getUserLimit() || $this->getUserLimit() == 0) )
					if( !$channel_exists )
					{
						$this->addSeparee($sep);	// Neues Separée anlegen
						$user->setLastLogin();		// Setze Benutzerlogin auf NOW()
						$this->addSystemMsg("<i><b>{$user->getUserName()}</b> verlässt den Channel!</i>");
						$this->setChannelId( $this->getChannelId($newchannel) );	// Aktuellen Channel auf ID setzen
						
						if( $user->getUserLevel() >= 10 ){
							$this->addUser( $user->getUserName(), $user->getUserLevel() );	// User dem Channel hinzufügen und aus dem alten entfernen
						}	
						elseif( $user->getUserLevel() == 1 ){
							$this->addUser( $user->getUserName(), 10 );	// User dem Channel hinzufügen und aus dem alten entfernen
						}
						$this->addSystemMsg("<i><b>{$user->getUserName()}</b> betritt den Channel!</i>",$newchannel);
					}
					else{
						$this->addErrorMsg("Ein Separée mit diesem Namen konnte nicht erstellt werden!");
					}
				}
			}
		
			// Invite
			elseif( mb_substr($input,0,5)=="/inv " && $user->getUserStatus() == 1 && $user->hasPrivilege("INVITE"))
			{
				$text = explode(" ",$input,2);				
				if( preg_match("#\<(.*?)\>#", html_entity_decode($text[1]), $matches) )
					$whisperto = $matches[1];
				else
					$whisperto = $text[1];
		
				if( $whisperto != "" && $whisperto != $user->getUserName() && $user->isOnline($whisperto) ){					
					
					$invite_text = $user->getUserName() ." hat dich in den Channel ". $this->getChannelName() ." eingeladen! Wenn du ihn betreten m&ouml;chtest klicke <a href=\"javascript:join(\'". $this->getChannelName() ."\');\">hier</a>.";
					$invite_text2 = "Du hast ". $text[1] ." eingeladen den Channel ". $this->getChannelName() ." zu betreten!";
					$this->addChatMsg($invite_text,"WHISPER","Chatbot","#000000", $whisperto);
					$this->addErrorMsg($invite_text2);
					$user->updateCharCount($strlen);
				}
				elseif($whisperto && $whisperto == $user->getUserName()){
					$this->addErrorMsg("Hast du lange Weile oder warum l&auml;dst du dich selber ein?");
				}
				elseif(!$user->isOnline($whisperto)){
					$this->addErrorMsg("Der Benutzer <b>". $whisperto . "</b> wurde nicht gefunden!");
				}
				else{
					$this->addErrorMsg("Und WEN willst du einladen?");
				}
			}
		
			// AWAY
			elseif(substr($input,0,5)=="/away" && $user->getUserStatus() == 1)
			{
				$text = str_replace("/away", "", mb_substr($input,0,230));
		
				if(!$user->isAway() && trim($text) != ""){
					$user->setAway(trim($text));
					$this->addSystemMsg("<i><b>". $user->getUserName() ."</b> ist nun abwesend! Grund: ". $text ."</i>");
				}
				elseif(!$user->isAway() && trim($text) == "" ){
					$user->setAway("Der Benutzer hat keine Nachricht hinterlassen.");
					$this->addSystemMsg("<i><b>". $user->getUserName() ."</b> ist nun abwesend!</i>");
				}
				else {
					$user->setAway("");
					$this->addSystemMsg("<i><b>". $user->getUserName() ."</b> ist wieder da!</i>");
				}
			}
			
			// Color
			elseif(substr($input,0,7)=="/color " ){
				
				$text = explode(" ",$input,2);
				$color = $this->clean($text[1]);
				
				if( preg_match('~^#?([0-9a-f]{3}\b|[0-9a-f]{6}\b)$~i',$color) > 0 ){
					$user->setUserColor($color);
				}
				else{
					$this->addErrorMsg("Ungültige Farbe: {$color} (Bsp: #000000)");
				}
			}
		
			// Quit
			elseif(substr($input,0,5)=="/quit" )
			{
				$user->logout();
			}
		
			//########### OPERATOR-Commands ###########//		
			// Bot-Request
			elseif(substr($input,0,11)=="/botrequest" && $user->hasPrivilege("REQUEST") )
			{
				if(!$user->isInChannel("Chatbot")){
					$this->addChatbot($this->getChannelName());
					$this->addSystemMsg("<i><b>Chatbot</b> betritt den Channel!</i>");
				}			
				else{
					$this->addSystemMsg("Der Chatbot ist bereits in diesem Channel!");
				}
			}
		
			// Bot-Remove
			elseif(substr($input,0,10)=="/botremove" && $user->hasPrivilege("REQUEST"))
			{
				if($user->isInChannel("Chatbot")){
					$this->removeChatbot($this->getChannelName());
					$this->addSystemMsg("<i><b>Chatbot</b> verlässt den Channel!</i>");
				}			
				else{
					$this->addSystemMsg("Der Chatbot ist nicht in diesem Channel!");
				}
			}
		
			// Chatbot Command
			elseif(substr($input,0,9)=="/chatbot " && $user->hasPrivilege("CHATBOT"))
			{
				if($user->isInChannel("Chatbot")){
					$text = str_replace("/chatbot ", "",$input);
					$this->addSystemMsg($text);
				}
			}
		
			// Voice
			elseif( mb_substr($input,0,3)=="/v " && $user->getUserStatus() == 1 && $user->hasPrivilege("VOICE"))
			{
				$text = explode(" ",$input,2);
				if( preg_match("#\<(.*?)\>#", html_entity_decode($text[1]), $matches) )
					$username = $matches[1];
				else
					$username = $text[1];
		
				if($user->isInChannel($username) && !$user->isChatAdmin($username)){
					$user->setOnlineLevel($username, 2);
					$this->addSystemMsg("<b>". $username ."</b> wurden von <b>". $user->getUserName() . "</b> Voice-Rechte verliehen!");
				}			
				elseif($user->isChatAdmin($username)){
					$this->addErrorMsg("Der Benutzer <b>". $username . "</b> hat bereits einen höheren Level!");
				}			
				else{
					$this->addErrorMsg("Der Benutzer <b>". $username . "</b> wurde nicht gefunden!");
				}
			}
		
			// Unvoice
			elseif( mb_substr($input,0,4)=="/uv " && $user->getUserStatus() == 1 && $user->hasPrivilege("UNVOICE"))
			{
				$text = explode(" ",$input,2);
				if( preg_match("#\<(.*?)\>#", html_entity_decode($text[1]), $matches) )
					$username = $matches[1];
				else
					$username = $text[1];
		
				if($user->isInChannel($username) && !$user->isChatAdmin($username)){
					$user->setOnlineLevel($username, 1);
					$this->addSystemMsg("<b>". $username ."</b> wurden von <b>". $user->getUserName() . "</b> die Voice-Rechte entzogen!");
				}			
				elseif($user->isChatAdmin($username)){
					$this->addErrorMsg("Der Benutzer <b>". $username . "</b> hat bereits einen höheren Level!");
				}			
				else{
					$this->addErrorMsg("Der Benutzer <b>". $username . "</b> wurde nicht gefunden!");
				}
			}
		
			// Kick
			elseif(substr($input,0,6)=="/kick " && $user->getUserStatus() == 1 && $user->hasPrivilege("KICK"))
			{
				$text = explode(" ",$input);
				if( preg_match("#\<(.*?)\>#", html_entity_decode($input), $matches) ){
					$username = $matches[1];
					$grund = str_replace("&#60;".$matches[1]."&#62;","",$input);
					$grund = mb_substr($grund,6);
				}
				else{
					$username = $text[1];
					$grund = mb_substr($input,mb_strlen($text[0])+mb_strlen($text[1])+2);
				}
		
				if($user->exists($username) && $user->isOnline($username) && !$user->isChatAdmin($username))
				{
					if( $grund != "" ){
						$this->addSystemMsg("<b>". $username ."</b> wurde von <b>{$user->getUserName()}</b> aus dem Channel gekickt! Grund: {$grund}");
					}
					elseif( $grund == "" ){
						$this->addSystemMsg("<b>". $username ."</b> wurde von <b>{$user->getUserName()}</b> aus dem Channel gekickt!");
					}
					$this->removeUser($username);
					$user->logout($username);	// Benutzer als ausgeloggt setzen
				}
				elseif($user->exists($username) && $user->isOnline($username) && $user->isChatAdmin($username)){
					$this->addErrorMsg("Der Benutzer <b>". $username . "</b> ist Administrator und kann nicht gekickt werden!");
				}
				else{
					$this->addErrorMsg("Der Benutzer <b>". $username . "</b> wurde nicht gefunden!");
				}
			}
		
			// Ban
			elseif(substr($input,0,5)=="/ban " && $user->getUserStatus() == 1 && $user->hasPrivilege("BAN"))
			{
				$text = explode(" ",$input);
				if( preg_match("#\<(.*?)\>#", html_entity_decode($input), $matches) ){
					$username = $matches[1];
					$grund = str_replace("&#60;".$matches[1]."&#62;","",$input);
					$grund = mb_substr($grund,5);
				}
				else{
					$username = $text[1];
					$grund = mb_substr($input,mb_strlen($text[0])+mb_strlen($text[1])+2);
				}
		
				if($user->isOnline($username) && !$user->isChatAdmin($username))
				{
					if( $grund != "" ){
						$this->addSystemMsg("<b>". $username ."</b> wurde von <b>". $user->getUserName() . "</b> aus dem Channel gekickt und aus dem Chat verbannt! Grund: {$grund}");
					}
					elseif( $grund == "" )	{
						$this->addSystemMsg("<b>". $username ."</b> wurde von <b>". $user->getUserName() . "</b> aus dem Channel gekickt und aus dem Chat verbannt!");
					}
					$this->removeUser($username);
					$user->setBanned($username);	// Benutzerstatus auf "verbannt" setzen
				}
				elseif($user->isOnline($username) && $user->isChatAdmin($username)){
					$this->addErrorMsg("Der Benutzer <b>". $username . "</b> ist Administrator und kann nicht verbannt werden!");
				}
				else{
					$this->addErrorMsg("Der Benutzer <b>". $username . "</b> wurde nicht gefunden!");
				}
			}
		
			// Mod
			elseif(substr($input,0,4)=="/mod" && $user->getUserStatus() == 1 && $user->hasPrivilege("MOD"))
			{
				if($this->isModerated()){
					$this->setModerated($this->getChannelName(), 0);
					$this->addSystemMsg("Dieser Channel ist nun nicht mehr moderiert!");
				}			
				elseif(!$this->isModerated()){
					$this->setModerated($this->getChannelName(), 1);
					$this->addSystemMsg("Dieser Channel ist nun moderiert!");
				}
			}
		
			// Hidden
			elseif(substr($input,0,7)=="/hidden" && $user->getUserStatus() == 1 && $user->hasPrivilege("HIDDEN"))
			{
				if($this->isHidden()){
					$this->setHidden($this->getChannelName(), 0);
					$this->addSystemMsg("Dieser Channel ist nun nicht mehr versteckt!");
				}			
				elseif(!$this->isHidden()){
					$this->setHidden($this->getChannelName(), 1);
					$this->addSystemMsg("Dieser Channel ist nun versteckt!");
				}
			}
		
			// Limit
			elseif(substr($input,0,7)=="/limit " && $user->getUserStatus() == 1 && $user->hasPrivilege("LIMIT"))
			{
				$text = explode(" ",$input);
				$limit = (int)$text[1];
		
				if($limit > 0 ){
					$this->setUserLimit($this->getChannelName(), $limit);
					$this->addSystemMsg("Dieser Channel ist nun auf ". $limit ." Benutzer limitiert!");
				}			
				elseif($limit == 0 && $this->getUserLimit() >= 1 ){
					$this->setUserLimit($this->getChannelName(), 0);
					$this->addSystemMsg("Dieser Channel hat nun kein Benutzerlimit mehr!");
				}
			}
			
			// Silent
			elseif( mb_substr($input,0,3)=="/s " && $user->getUserStatus() == 1 && $user->hasPrivilege("SILENCE") )
			{
				$text = explode(" ",$input);
				if( preg_match("#\<(.*?)\>#", html_entity_decode($input), $matches) ){
					$username = $matches[1];
					$time = str_replace("&#60;".$matches[1]."&#62;","",$input);
					$time = mb_substr($time,3);
				}
				else{
					$username = $text[1];
					$time = mb_substr($input,mb_strlen($text[0])+mb_strlen($text[1])+2);					
				}

				if( $username != "" && $time > 0 && $user->exists($username) && !$user->isChatAdmin($username) ){
					$user->setSilence($username,($time*60)+time());
					$this->addSystemMsg("<b>".$username."</b> wurde von {$user->getUserName()} f&uuml;r {$time} Minuten die Redeerlaubnis entzogen!");
				}
				elseif( $username != "" && $time == 0 && $user->exists($username) ){
					$user->setSilence($username,time());
					$this->addSystemMsg($username . " darf nun wieder sprechen!");
				}
				elseif( $user->isChatAdmin($username) ){
					$this->addErrorMsg("Dieser Benutzer lässt sich das Reden nicht verbieten!");
				}
				else{
					$this->addErrorMsg("Der Benutzer <b>". $username . "</b> wurde nicht gefunden!");
				}
			}
		
			//########### ADMINISTRATOR-Commands ###########//		
			// BROADCAST
			elseif(substr($input,0,4)=="/bc " && $user->getUserStatus() == 1 && $user->hasPrivilege("BROADCAST")){
					$input = str_replace("/bc ", "",$input);
					$this->addBroadcastMsg($input);
			}
						
			// Op
			elseif(substr($input,0,4)=="/op " && $user->getUserStatus() == 1 && $user->hasPrivilege("OP"))
			{
				$text = explode(" ",$input,2);
				if( preg_match("#\<(.*?)\>#", html_entity_decode($text[1]), $matches) )
					$username = $matches[1];
				else
					$username = $text[1];
		
				if($user->exists($username) && $user->isOnline($username) && !$user->isChatAdmin($username)){
					$user->setUserLevel($username,20);
					$user->setOnlineLevel($username,20);					
		
					$this->addSystemMsg("<b>". $username ."</b> wurden von <b>". $user->getUserName() . "</b> Operator-Rechte verliehen!");
				}			
				elseif($user->isChatAdmin($username)){
					$this->addErrorMsg("Der Benutzer <b>". $username . "</b> hat bereits einen höheren Level!");
				}			
				else{
					$this->addErrorMsg("Der Benutzer <b>". $username . "</b> wurde nicht gefunden!");
				}
			}				
		
			// DeOp
			elseif(substr($input,0,6)=="/deop " && $user->getUserStatus() == 1 && $user->hasPrivilege("DEOP"))
			{
				$text = explode(" ",$input,2);
				if( preg_match("#\<(.*?)\>#", html_entity_decode($text[1]), $matches) )
					$username = $matches[1];
				else
					$username = $text[1];
		
				if($user->exists($username) && $user->isOnline($username) && !$user->isChatAdmin($username)){
					$user->setOnlineLevel($username, 1);
					$user->setUserLevel($username, 1);		
					$this->addSystemMsg("<b>". $username ."</b> wurden von <b>". $user->getUserName() . "</b> die Operator-Rechte entzogen!");
				}			
				elseif($user->isChatAdmin($username)){
					$this->addErrorMsg("Der Benutzer <b>". $username . "</b> ist Administrator und kann nicht entmachtet werden!");
				}			
				else{
					$this->addErrorMsg("Der Benutzer <b>". $username . "</b> wurde nicht gefunden!");
				}
			}			
		
			// ChanOp
			elseif(substr($input,0,8)=="/chanop " && $user->getUserStatus() == 1 && $user->hasPrivilege("CHANOP"))
			{
				$text = explode(" ",$input,2);
				if( preg_match("#\<(.*?)\>#", html_entity_decode($text[1]), $matches) )
					$username = $matches[1];
				else
					$username = $text[1];
		
				if($user->exists($username) && $user->isOnline($username) && !$user->isChatAdmin($username)){
					$user->setOnlineLevel($username, 10);
					$this->addSystemMsg("<b>". $username ."</b> wurden von <b>". $user->getUserName() . "</b> Channel-Operator-Rechte verliehen!");
				}
				elseif($user->isChatAdmin($username)){
					$this->addErrorMsg("Der Benutzer <b>". $username . "</b> hat bereits einen höheren Level!");
				}
				else{
					$this->addErrorMsg("Der Benutzer <b>". $username . "</b> wurde nicht gefunden!");
				}
			}
		
			// DeChanOp
			elseif(substr($input,0,10)=="/dechanop " && $user->getUserStatus() == 1 && $user->hasPrivilege("DECHANOP"))
			{
				$text = explode(" ",$input,2);
				if( preg_match("#\<(.*?)\>#", html_entity_decode($text[1]), $matches) )
					$username = $matches[1];
				else
					$username = $text[1];
		
				if($user->exists($username) && $user->isOnline($username) && !$user->isChatAdmin($username)){
					$user->setOnlineLevel($username, 1);	// Benutzerlevel auf 1 setzen
					$this->addSystemMsg("<b>". $username ."</b> wurden von <b>". $user->getUserName() . "</b> die Channel-Operator-Rechte entzogen!");
				}			
				elseif($user->isChatAdmin($username)){
					$this->addErrorMsg("Der Benutzer <b>". $username . "</b> ist Administrator und kann nicht entmachtet werden!");
				}			
				else{
					$this->addErrorMsg("Der Benutzer <b>". $username . "</b> wurde nicht gefunden!");
				}
			}
		
			// Unban
			elseif(substr($input,0,7)=="/unban " && $user->getUserStatus() == 1 && $user->hasPrivilege("UNBAN"))
			{
				$text = explode(" ",$input,2);
				if( preg_match("#\<(.*?)\>#", html_entity_decode($text[1]), $matches) )
					$username = $matches[1];
				else
					$username = $text[1];
		
				if($user->exists($username) && $user->isOnline($username)){
					$this->addSystemMsg("Der Chatban von <b>". $username ."</b> wurde von <b>". $user->getUserName() . "</b> aufgehoben!");
					$user->setUserStatus($username, 0);	// Benutzer "entbannen"
				}			
				else{
					$this->addErrorMsg("Der Benutzer <b>". $username . "</b> wurde nicht gefunden!");
				}
			}		
			######################			
		}
		// Chat-Message
		elseif( $user->getUserStatus() == 1 && $user->hasPrivilege("WRITE") && !$user->isSilent() && trim($input) != "" )
		{
			if(!$this->isModerated() || $this->isModerated() && $user->hasPrivilege("MODERATE"))
			{			
				if(!$user->isAway()){					
					$this->addChatMsg($input);
					$user->updateCharCount($strlen);
				}
				elseif($user->isAway()){					
					$user->setAway("");	// Away-Status deaktivieren
					$this->addSystemMsg("<i><b>". $user->getUserName() ."</b> ist wieder da!</i>");
					$this->addChatMsg($input);
					$user->updateCharCount($strlen);				
				}
			}
			else {
				$this->addErrorMsg("Sie haben in diesem Channel keine Schreibrechte!");
			}
		}
		elseif( $user->isSilent() ){
			$silence = date("d.m.Y H:i:s",$user->getUserSilence());
			$this->addErrorMsg("Ihnen wurde bis {$silence} Redeverbot erteilt!");
		}		
	}
	
	
	/**
	 * Sends a chat message to the current or given channel
	 * @param String $input
	 * @param String $channel optional
	 * @param String $username optional
	 * @param String $color optional
	 * @param String $whisperto optional
	 * @return void
	 */
	protected function addChatMsg( $input, $channel = "", $username = "", $color = "", $whisperto = "" ){
		
		global $db;
		
		$user = new User();
		$channel = ( $channel == "" )	?	$this->getChannelName()	:	$channel;
		$username = ( $username == "" )	?	$user->getUserName()	:	$username;
		$color = ( $color == "" )		?	$user->getUserColor()	:	$color;
		
		if( $whisperto == "self" ){
			$whisperto = $user->getUserName();
		}
		
		// Smileys umwandeln
		$input = $this->convertUrl($input);
		$input = $this->convertSmileys($input);

		// Chatinput
		$this->query("INSERT INTO {$db["postings"]} (username,message,channel,whisperto,color,time) VALUES ('{$username}', '{$input}', '{$channel}', '{$whisperto}', '{$color}', UNIX_TIMESTAMP())");	
	}
	
	
	/**
	 * Sends a System-Message to the current channel
	 * @param String $input
	 * @param String $channel
	 * @return void
	 */
	public function addSystemMsg( $input, $channel = "" ){
		$this->addChatMsg( $input, $channel, "Chatbot", "#000000" );
	}
	
	
	/**
	 * Sends an error message to the current user
	 * @param $input
	 * @param String $whisperto
	 * @return void
	 */
	public function addErrorMsg( $input ){
		$this->addChatMsg( $input, "SYSTEM", "Chatbot", "#800000", "self" );
	}
	
	
	/**
	 * Sends a private message from the current user to another user
	 * @param $input
	 * @param $whisperto
	 * @return void
	 */
	public function addPrivateMsg( $input, $whisperto ){
		$this->addChatMsg($input, "WHISPER", "", "", $whisperto);
	}
	
	
	/**
	 * Sends a Broadcast-Message to the current channel
	 * @param $input
	 * @return void
	 */
	public function addBroadcastMsg( $input ){
		$this->addChatMsg($input, "BROADCAST");
	}
}
?>